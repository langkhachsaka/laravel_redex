<?php

namespace Modules\WarehouseReceivingVN\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Modules\Base\Http\Controllers\Controller;
use Modules\Base\Models\Setting;
use Modules\Task\Models\ReceiveShipmentTask;
use Modules\Task\Models\VerifyLadingCodeTask;
use Modules\VerifyLadingCode\Models\VerifyLadingCode;
use Modules\WarehouseReceivingVN\Models\WarehouseReceivingVN;
use Modules\Shipment\Models\Shipment;
use Modules\WarehouseReceivingVN\Models\WarehouseVnLadingItem;

class WarehouseReceivingVNController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorize('index', WarehouseReceivingVN::class);

        $user_receive_id = auth()->user()->isVietnameseShippingOfficer()
            ? auth()->id()
            : $request->input('user_receive_id');

        $perPage = $this->getPerPage($request);

        /*$wReceiving = WarehouseReceivingVN::with('warehouse','userReceive','shipment','shipment.userCreator','shipment.warehouse','shipment.shipmentItem'
            ,'warehouseVnLadingItems','shipment.shipmentItem.ladingCodes','shipment.shipmentItem.warehouseVnLadingItem',
            'shipment.shipmentItem.ladingCodes.billCode','shipment.shipmentItem.ladingCodes.billCode. customerOrder','shipment.shipmentItem.ladingCodes.billCode.customerOrder.customer')
            ->WhereFullLike('shipment_code', $request->input('shipment_code'))
            ->filterWhere('date_receiving', '>=', $request->input('date_receiving_from'))
            ->filterWhere('date_receiving', '<=', $request->input('date_receiving_to'))
            ->filterWhere('user_receive_id', '=', $user_receive_id)
            ->filterWhere('warehouse_id', '=', $request->input('warehouse_id'))
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);*/
        $wReceivingItem = WarehouseVnLadingItem::with('subLadingCode','subLadingCode.customerOrder','subLadingCode.ladingCodes'
            ,'subLadingCode.ladingCodes.billCodeOut.customerOrder',
            'subLadingCode.customerOrder.customer','warehouseReceivingVN','verifyLadingCode','ladingCodes','ladingCodes.billCodeOut.customerOrder',
            'ladingCodes.billOfLading','ladingCodes.billOfLading.customer',
            'ladingCodes.billCodeOut.customerOrder.customer','warehouseReceivingVN.userReceive')->orderBy('created_at', 'desc')
            ->where('status','<>',WarehouseVnLadingItem::DISABLED)
            ->paginate($perPage);
        /*$error = Setting::where('status','=','1')->first();*/
        return $this->respondSuccessData(['data' => $wReceivingItem,'error' => []]);
    }


    /**
     * Show detail for check and split
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {

        $wReceiving = WarehouseReceivingVN::with('warehouse','userReceive','shipment','shipment.userCreator','shipment.warehouse','shipment.shipmentItem'
            ,'warehouseVnLadingItems','warehouseVnLadingItems.subLadingCode','warehouseVnLadingItems.haveSubLadingCode','shipment.shipmentItem.ladingCodes','shipment.shipmentItem.warehouseVnLadingItem',
            'shipment.shipmentItem.ladingCodes.billCode','shipment.shipmentItem.ladingCodes.billCode.customerOrder','shipment.shipmentItem.ladingCodes.billCode.customerOrder.customer')
            ->findOrFail($id);
        $factorConversion = \Modules\Setting\Models\Setting::select('factor_conversion')->first();
        return $this->respondSuccessData(['data'=>$wReceiving,'factor_conversion' => $factorConversion->factor_conversion]);
    }

    /**
     * @param Request $request
     *
     */
    public function createTaskVerify(Request $request){
        try {
            DB::beginTransaction();
            $ladingCodes = $request->lading_code;
            WarehouseVnLadingItem::whereIn('lading_code',$ladingCodes)->update(['status' => WarehouseVnLadingItem::STATUS_WAIT_TEST]);
            foreach ($ladingCodes as $ladingCode){
                $verifyLadingCode = new VerifyLadingCode();
                $verifyLadingCode->lading_code = $ladingCode;
                $verifyLadingCode->verifier_id = $request->verifier_id;
                $verifyLadingCode->status = VerifyLadingCode::NOT_YET_VERIFY;
                $verifyLadingCode->save();
            }

            //CREATE TASK FOR VERIFIER
            VerifyLadingCodeTask::newTaskVerifyLadingCode(implode(" ",$ladingCodes),$request->verifier_id);
            //END
            $warehouseVNItems = WarehouseVnLadingItem::with('warehouseReceivingVN','verifyLadingCode','ladingCodes','ladingCodes.billCodeOut.customerOrder',
            'ladingCodes.billCodeOut.customerOrder.customer','warehouseReceivingVN.userReceive')->whereIn('lading_code',$ladingCodes)->get();

            DB::commit();
            return $this->respondSuccessData($warehouseVNItems, 'Tạo nhiệm vụ kiểm hàng thành công');
        }catch (\Exception $e){
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Save data temporary for re check
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveTemp(Request $request, $id){

        try{
            DB::beginTransaction();
            // IF unmatch => report to admin
            WarehouseReceivingVN::findOrFail($id)->update(['status'=> WarehouseReceivingVN::STATUS_REPORTED]);

            /*
             * Coding for notification to admin
             */

            WarehouseVnLadingItem::where('warehouse_receiving_v_ns_id','=',$id)->delete();
            $ladingItems = $request->ladings;
            foreach ($ladingItems as $ladingItem){
                $warehouseVnLadingItem = new WarehouseVnLadingItem();
                $warehouseVnLadingItem->warehouse_receiving_v_ns_id = $id;
                $warehouseVnLadingItem->fill($ladingItem);
                if(!isset($ladingItem["pack"])){
                    $warehouseVnLadingItem->pack = WarehouseVnLadingItem::UNPACK;
                }
                $warehouseVnLadingItem->status = WarehouseVnLadingItem::STATUS_TEMPORARY;
                $warehouseVnLadingItem->save();
            }
            $wReceiving = WarehouseReceivingVN::with('warehouse','userReceive','shipment','shipment.userCreator','shipment.warehouse','shipment.shipmentItem'
                ,'warehouseVnLadingItems','shipment.shipmentItem.ladingCodes','shipment.shipmentItem.warehouseVnLadingItem',
                'shipment.shipmentItem.ladingCodes.billCode','shipment.shipmentItem.ladingCodes.billCode.customerOrder','shipment.shipmentItem.ladingCodes.billCode.customerOrder.customer'
            ,'shipment.shipmentItem.ladingCodes.billOfLading','shipment.shipmentItem.ladingCodes.billOfLading.customer')
                ->findOrFail($id);
            DB::commit();
            return $this->respondSuccessData($wReceiving,'Lưu tạm thành công');
        }catch (\Exception $e){
            DB::rollBack();
            throw $e;
        }

    }

    /**
     * Save data.
     *
     * @param $id
     */
    public function submitData($id){
        try{
            DB::beginTransaction();
            $wReceive = WarehouseReceivingVN::findOrFail($id);
            $wReceive->status =  WarehouseReceivingVN::STATUS_CONFIRMED;
            $wReceive->save();
            $shipment = Shipment::where('shipment_code','=',$wReceive->shipment_code)->first();
            $shipment->status = Shipment::STATUS_RECIEVED_MATCH;
            $shipment->save();
            WarehouseVnLadingItem::where('warehouse_receiving_v_ns_id','=',$id)->update(['status' => WarehouseVnLadingItem::STATUS_SUBMITED]);

            //BEGIN UPDATE STATUS OF TASK FOR RECEIVING SHIPMENT
            ReceiveShipmentTask::updateComplaintByUser($wReceive);
            //END
            DB::commit();
            return $this->respondSuccessData([],'Xác nhận thành công');
        }catch (\Exception $e){
            throw $e;
            DB::rollBack();
        }
    }
    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request)
    {
        $this->authorize('create', WarehouseReceivingVN::class);

        $wReceiving = new WarehouseReceivingVN();

        $requestData = $request->all();
        /*$validator = $this->validateRequestData($requestData);
        if ($validator->fails()) {
            return $this->respondInvalidData($validator->messages());
        }*/
        $wReceiving->fill($requestData);
        if(strpos($requestData['shipment_code'],Shipment::PREFIX_SHIPMENT_CODE) == false) {
            $wReceiving->shipment_code = self::getShipmentCode($requestData['shipment_code']);
        }
        $wReceiving->status = '1';
        $wReceiving->save();
        $wReceiving->load(
            'warehouse','userReceive','shipment','shipment.userCreator','shipment.warehouse','shipment.shipmentItem'
            ,'warehouseVnLadingItems','shipment.shipmentItem.ladingCodes','shipment.shipmentItem.warehouseVnLadingItem',
            'shipment.shipmentItem.ladingCodes.billCode','shipment.shipmentItem.ladingCodes.billCode.customerOrder','shipment.shipmentItem.ladingCodes.billCode.customerOrder.customer'
        );
        return $this->respondSuccessData($wReceiving, 'Thêm mới thành công');
    }


    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request,$id)
    {
        try{
            $this->authorize('update', WarehouseReceivingVN::class);

            DB::beginTransaction();
            /** @var WarehouseReceivingCN $wReceiving */
            $wReceiving = WarehouseReceivingVN::findOrFail($id);
            $requestData = $request->all();
            /*$validator = $this->validateRequestData($requestData);
            if ($validator->fails()) {
                return $this->respondInvalidData($validator->messages());
            }*/
            $oldStatus = $wReceiving->status;
            $wReceiving->fill($requestData);
            $wReceiving->status = $oldStatus;

            if(strpos($requestData['shipment_code'],Shipment::PREFIX_SHIPMENT_CODE) === false) {
                $wReceiving->shipment_code = self::getShipmentCode($requestData['shipment_code']);
            }
            $wReceiving->save();
            DB::commit();
            $wReceiving->load(
                'warehouse','userReceive','shipment','shipment.userCreator','shipment.warehouse','shipment.shipmentItem'
                ,'warehouseVnLadingItems','shipment.shipmentItem.ladingCodes','shipment.shipmentItem.warehouseVnLadingItem',
                'shipment.shipmentItem.ladingCodes.billCode','shipment.shipmentItem.ladingCodes.billCode.customerOrder','shipment.shipmentItem.ladingCodes.billCode.customerOrder.customer'
            );
            return $this->respondSuccessData($wReceiving, 'Cập nhật thành công');
        } catch (\Exception $e){
            DB::rollBack();
        }

    }

    public function checkShipmentCode($id){
        $shipment = Shipment::with('warehouseVn')->where('shipment_code','=',$id)->first();
        return $this->respondSuccessData($shipment);
    }

    /**
     * @param Request $request
     * @param $shipment_code
     * @return \Illuminate\Http\JsonResponse
     * Store shipment when click "Bắt đầu "
     */
    public function storeShipment(Request $request,$shipment_code){

        try{
            DB::beginTransaction();
            $userReceiveId = auth()->user()->id;
            $warehouseId = auth()->user()->warehouse_id;
           if(WarehouseReceivingVN::where('shipment_code',$shipment_code)->exists()){
                $warehouseVN = WarehouseReceivingVN::where('shipment_code',$shipment_code)->first();
                $warehouseVN->shipment_code = $shipment_code;
                $warehouseVN->warehouse_id = $warehouseId;
                $warehouseVN->date_receiving = date('Y/m/d h:i:s');
                $warehouseVN->status = WarehouseReceivingVN::STATUS_NOT_YET_CONFIRM;
                $requestData = $request->all();
                $warehouseVN->fill($requestData);
                if(!isset($requestData["pack"])){
                    $warehouseVN->pack = 0;
                }

                $warehouseVN->save();
                $shipment = Shipment::where('shipment_code','=', $shipment_code)->first();
                $shipment->receive_date = date('Y/m/d h:i:s');
                $shipment->save();
            } else {

                $warehouseVN = new WarehouseReceivingVN();
                $warehouseVN->shipment_code = $shipment_code;
                $warehouseVN->warehouse_id = $warehouseId;
                $warehouseVN->user_receive_id = $userReceiveId;
                $warehouseVN->date_receiving = date('Y/m/d h:i:s');
                $warehouseVN->status = WarehouseReceivingVN::STATUS_NOT_YET_CONFIRM;
                $requestData = $request->all();
                $warehouseVN->fill($requestData);
                if(!isset($requestData["pack"])){
                    $warehouseVN->pack = 0;
                }
                $warehouseVN->save();

                $shipment = Shipment::where('shipment_code','=', $shipment_code)->first();
                $shipment->receive_date = date('Y/m/d h:i:s');
                $shipment->save();
            }


            DB::commit();
            return $this->respondSuccessData($warehouseVN->id);
        } catch (\Exception $e){
            DB::rollBack();
            throw $e;
        }

    }
    /**
     * Get shipment code by id
     * @return string
     *
     */
    private static function getShipmentCode($id){
        $shipment = Shipment::findOrFail($id);
        return $shipment->shipment_code;
    }

    /**
     * Approve shipment MATCH
     * @return string
     *
     */
    public function approveShipment(Request $request){
        try{

            DB::beginTransaction();
            $wReceive = WarehouseReceivingVN::findOrFail($request->id);
            $wReceive->status = WarehouseReceivingVN::STATUS_CONFIRMED;
            $wReceive->save();

            $shipment = Shipment::where('shipment_code','=',$wReceive->shipment_code)->first();
            $shipment->status = Shipment::STATUS_RECIEVED_MATCH;
            $shipment->save();
            DB::commit();
            $wReceive->load('warehouse','userReceive','shipment','shipment.userCreator','shipment.warehouse','shipment.shipmentItem','shipment.shipmentItem','shipment.shipmentItem.billOfLading','shipment.shipmentItem.billOfLading.ladingCode');
            return $this->respondSuccessData($wReceive, 'Xác nhận thành công');
        } catch (\Exception $e){
            DB::rollBack();
        }

    }

    /**
     * REPORT SHIPMENT_UNMATCH.
     * @return string
     *
     */
    public function reportShipment(Request $request){
        $formProps = $request[0];
        $fieldErrors = $request[1];
        try{
            DB::beginTransaction();
            $wReceive = WarehouseReceivingVN::findOrFail($formProps['id']);
            $wReceive->status = WarehouseReceivingVN::STATUS_REPORTED;
            $wReceive->save();

            $shipment = Shipment::where('shipment_code','=',$wReceive->shipment_code)->first();

            $shipment->status = Shipment::STATUS_RECIEVED_UNMATCH;
            $shipment->note = '('. date("Y-m-d H:i:s") .') Hàng phía VN nhận được không khớp về <b>'.$fieldErrors.'</b>.<br/> <i>Khối lượng thực nhận được :</i> '.$formProps['weight'].
                '(kg).<br/><i>Chiều cao : </i>'.$formProps['height'].' (cm),<i> Chiều rộng :</i> '.$formProps['width'].
                ' (cm),<i> Chiều dài : </i>'.$formProps['length'].' (cm)';
            $shipment->save();
            DB::commit();
            $wReceive->load('warehouse','userReceive','shipment','shipment.userCreator','shipment.warehouse','shipment.shipmentItem','shipment.shipmentItem','shipment.shipmentItem.billOfLading','shipment.shipmentItem.billOfLading.ladingCode');
            return $this->respondSuccessData($wReceive, 'Đã báo cáo');

        } catch (\Exception $e){
            DB::rollBack();
            throw $e;
        }

    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $this->authorize('delete', WarehouseReceivingVN::class);
        try{
            DB::beginTransaction();
            $wReceive = WarehouseReceivingVN::findOrFail($id);
            $shipment = Shipment::where('shipment_code','=',$wReceive->shipment_code)->first();
            $shipment->status = Shipment::STATUS_DONE;
            $shipment->receive_date = null;
            $shipment->save();
            $wReceive->status = WarehouseReceivingVN::STATUS_NOT_YET_RECEIVE;
            $wReceive->save();
            WarehouseVnLadingItem::where('warehouse_receiving_v_ns_id','=',$id)->delete();
            DB::commit();
            return $this->respondSuccessData([], 'Xóa thành công');
        } catch (\Exception $e){
            DB::rollBack();
        }
        /** @var WarehouseReceivingCN $wReceiving */

    }
}
