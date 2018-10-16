<?php

namespace Modules\Shipment\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Modules\Base\Http\Controllers\Controller;
use Modules\Base\Models\Setting;
use Modules\Shipment\Models\Shipment;
use Modules\Shipment\Models\ShipmentItem;
use Modules\Task\Models\ReceiveShipmentTask;
use Modules\WarehouseReceivingCN\Models\WarehouseReceivingCN;
use Modules\WarehouseReceivingVN\Models\WarehouseReceivingVN;


class ShipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $perPage = $this->getPerPage($request);

        if($request->transport_type == Shipment::NOT_DEFINE_TRANSPORT) {
            $shipment = Shipment::with('userCreator','warehouseVn', 'shipmentItem', 'warehouse', 'shipmentItem.billOfLading','shipmentItem.billOfLading.ladingCode')
                ->whereFullLike('shipment_code', $request->shipment_code)
                ->filterWhere('warehouse_id', '=', $request->warehouse_id)
                ->whereNull('transport_type')
                ->filterWhere('status','=', $request->status)
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);
            return $this->respondSuccessData($shipment);
        } else {
            $shipment = Shipment::with('userCreator', 'warehouseVn', 'shipmentItem', 'warehouse', 'shipmentItem.billOfLading','shipmentItem.billOfLading.ladingCode')
                ->whereFullLike('shipment_code', $request->shipment_code)
                ->filterWhere('warehouse_id', '=', $request->warehouse_id)
                ->filterWhere('transport_type', '=', $request->transport_type)
                ->filterWhere('status','=', $request->status)
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);
            return $this->respondSuccessData($shipment);
        }

    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create(Request $request)
    {

        $shipment = new Shipment();

        $requestData = $request->all();
        /*$validator = $this->validateRequestData($requestData);
        if ($validator->fails()) {
            return $this->respondInvalidData($validator->messages());
        }*/
        try {
            DB::beginTransaction();
            $shipment->fill($requestData);
            $shipment->shipment_code = self::genShipmentCode();
            $shipment->status = Shipment::STATUS_NEW;
            $shipment->creator_id = auth()->user()->id;
            $shipment->save();
            $billOfLadingCodes =     explode(',',$request->bill_of_lading_codes);
            $countBillOfLadingCodes = count($billOfLadingCodes);
            if($countBillOfLadingCodes > 0) {
                for ($i = 0; $i < $countBillOfLadingCodes; $i++) {
                    $billOfLadingCode = str_replace(Shipment::NEW_BILL_OF_LADING_CODE_PREFIX, "", $billOfLadingCodes[$i]);
                    $shipmentItem = new ShipmentItem();
                    $shipmentItem->shipment_code = $shipment->shipment_code;
                    $shipmentItem->bill_of_lading_code = $billOfLadingCode;
                    $shipmentItem->save();
                }
            }
            DB::commit();
            $shipment->load('userCreator','warehouseVn','shipmentItem','warehouse', 'shipmentItem.billOfLading','shipmentItem.billOfLading.ladingCode');
            return $this->respondSuccessData($shipment, 'Thêm lô hàng mới thành công');
        }catch (\Exception $ex){
            DB::rollBack();
        }
    }

    /**
     * Get Shipment information
     */
    public function  getShipmentInfo(Request $request){
        $shipment = Shipment::with('userCreator','warehouse','shipmentItem')
            ->where('shipment_code', '=', $request->input('shipment_code'))
            ->first();
        return $this->respondSuccessData($shipment);
    }

    /**
     * Get list bill of lading not yet insert into shipment
     */
    public function  listBillOfLading(Request $request){
        $billdOfLadings = ShipmentItem::pluck('bill_of_lading_code')->all();
        $query = DB::table('warehouse_receiving_c_ns')
            ->join('lading_codes', 'warehouse_receiving_c_ns.bill_of_lading_code', '=', 'lading_codes.code')
            ->whereNotIn('lading_codes.code',$billdOfLadings)
            ->select('warehouse_receiving_c_ns.*', 'lading_codes.ladingcodetable_id',
                DB::raw("(CASE WHEN lading_codes.ladingcodetable_type = 'Modules\\\\CustomerOrder\\\\Models\\\\CustomerOrderItem' THEN 'Đơn hàng việt nam' ELSE 'Đơn hàng vận chuyển' END) as ladingcodetable_type"),
                DB::raw("CONCAT((CASE WHEN lading_codes.ladingcodetable_type = 'Modules\\\\CustomerOrder\\\\Models\\\\CustomerOrderItem' THEN 'Đơn hàng việt nam' ELSE 'Đơn hàng vận chuyển' END), 
                ' #',ladingcodetable_id,'. Mã vận đơn :  ',bill_of_lading_code, ' (', warehouse_receiving_c_ns.weight, ' kg)') as text"));

       /* $query = WarehouseReceivingCN::with('ladingCode','ladingCode.ladingcodetable')
            ->select(
                'warehouse_receiving_c_ns.*'
            )
                ->whereNotIn('bill_of_lading_code', $billdOfLadings)
                ->where('status', '=', '1');*/

        return ['results' => $query->get(['id', 'text','bill_of_lading_code','ladingcodetable_type','ladingcodetable_id','weight','height','width','length'])];
    }

    public function getNewShipmentCode(){
        $setting = Setting::first();
        return $this->respondSuccessData(['newShipmentCode' => self::genShipmentCode(),'rate' => $setting->rate  ]);
    }


    public function list(Request $request){
        $query = Shipment::query()->with('userCreator','warehouse','shipmentItem','shipmentItem.billOfLading')->limit(20);
        $query->whereFullLike('shipment_code', $request->input('q'));
        if($request->input('status') == Shipment::STATUS_NEW){
            $query->whereIn('status', [Shipment::STATUS_NEW,Shipment::STATUS_DONE]);
        }
        if($request->input('status') == Shipment::STATUS_DONE) {
            $wRecieveVN = WarehouseReceivingVN::pluck('shipment_code')->all();
            $query->whereNotIn('shipment_code', $wRecieveVN);
        }
        $query->orderBy('shipment_code','DESC');
        return ['results' => $query->get(['id', 'shipment_code as text','creator_id','shipment_code','warehouse_id','real_weight','transport_type'])];
    }

    /**
     * Store a newly created resource in storage.
     * Occur when click "tạo lô hàng" in " Kho hàng trung quốc"
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $shipment_code = $request->shipment_code;
        try {
            DB::beginTransaction();
            $shipment = new Shipment();

            if($request->is_new_shipment== "true"){
                $shipment->shipment_code = self::genShipmentCode();
                $shipment->transport_type = $request->transport_type;
                $shipment->real_weight = $request->real_weight;
                $shipment->volume = $request->volume;
                $shipment->warehouse_id = $request->warehouse_id;
                $shipment->creator_id = auth()->user()->id;
                $shipment->transport_date = date('Y/m/d h:i:s');
                $shipment->status  = Shipment::STATUS_DONE;
                $shipment->save();
                $shipment_code = $shipment->shipment_code;
            }

            $billOfLadingCodes =     explode(',',$request->bill_of_lading_codes);
            $countBillOfLadingCodes = count($billOfLadingCodes);
            for ($i = 0; $i < $countBillOfLadingCodes; $i++) {
                $billOfLadingCode =  $billOfLadingCodes[$i];
                $shipmentItem = new ShipmentItem();
                $shipmentItem->shipment_code = $shipment_code;
                $shipmentItem->bill_of_lading_code = $billOfLadingCode;
                $shipmentItem->save();
            }
            DB::commit();
            $warehouseCNs = WarehouseReceivingCN::whereIn('bill_of_lading_code',$billOfLadingCodes)->get();
            foreach ($warehouseCNs as $warehouseCN){
                $warehouseCN->load('warehouse','userReceive','shipmentItem','shipmentItem.shipment','ladingCode',
                    'ladingCode.billCode','ladingCode.billCode.customerOrder','ladingCode.billCode.customerOrder.customer');
            }
			return $this->respondSuccessData($warehouseCNs, 'Thêm vào lô hàng thành công');
	   } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    /**
     * store shipment item
     */
    public function storeShipmentItem(Request $request){
        $shipmentItem = new ShipmentItem();
        $shipmentItem->shipment_code = $request->shipment_code;
        $shipmentItem->bill_of_lading_code = $request->bill_of_lading_code;
        $shipmentItem->save();
        $shipmentItem->load('billOfLading');
        return $this->respondSuccessData($shipmentItem,'Thêm vào lô hàng thành công');
    }

    /**
     * store shipment item
     */
    public function createTaskReceiveShipment(Request $request){

        try{
            DB::beginTransaction();
            $shipmentCodes = $request->shipment_code;
            foreach($shipmentCodes as $shipmentCode){
                $warehouseReceivingVN = new WarehouseReceivingVN();
                $warehouseReceivingVN->shipment_code = $shipmentCode;
                $warehouseReceivingVN->user_receive_id = $request->user_receive_id;
                $warehouseReceivingVN->status = WarehouseReceivingVN::STATUS_NOT_YET_RECEIVE;
                $warehouseReceivingVN->save();

            }

            //CREATE TASK  RECEIVE SHIPMENT FOR VN_RECEIVE_SHIPPING_OFFICER
            ReceiveShipmentTask::newTaskReceiveShipment(implode(" ",$shipmentCodes),$request->user_receive_id);
            //END

            DB::commit();
            $shipments = Shipment::with('userCreator', 'warehouseVn', 'shipmentItem', 'warehouse', 'shipmentItem.billOfLading','shipmentItem.billOfLading.ladingCode')->whereIn('shipment_code',$shipmentCodes)->get();
            return $this->respondSuccessData($shipments,'Tạo nhiệm vụ nhập hàng vào kho VN thành công');
        } catch (\Exception $e){
            DB::rollBack();
            throw $e;
        }



    }
    /**
     * Auto generate new shipment_code
     * @return string
     *
     */
    private static function genShipmentCode(){
        $shipment = DB::table('shipments')->max('shipment_code');
        if(!$shipment) {
            return Shipment::NEW_SHIPMENT_CODE;
        }
        $shipmentInt = str_replace(Shipment::PREFIX_SHIPMENT_CODE,"",$shipment);
        $newShipmentInt = (int)$shipmentInt + 1;
        $newShipment = Shipment::PREFIX_SHIPMENT_CODE;
        for ($i = 0; $i < Shipment::NUM_CHAR_INT-strlen((string)$newShipmentInt); $i++) {
            $newShipment = $newShipment.'0';
        }
        $newShipment = $newShipment.(string)$newShipmentInt;
        return $newShipment ;
    }
    /**
     * Show the specified resource.
     * @return Response
     */
    public function deleteShipmentItem($id)
    {
        $shipmentItem = ShipmentItem::findOrFail($id);
        $shipmentItem->delete();
        return $this->respondSuccessData([], 'Xóa thành công');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('shipment::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request, $id)
    {

        try{
            DB::beginTransaction();
            $shipment = Shipment::findOrFail($id);
            $oldShipmentStatus = $shipment->status;
            $requestData = $request->all();
            /*$validator = $this->validateRequestData($requestData);
            if ($validator->fails()) {
                return $this->respondInvalidData($validator->messages());
            }*/
            $shipment->fill($requestData);

            $billOfLadingCodes = explode(',',$request->bill_of_lading_codes);
            $countBillOfLadingCodes = count($billOfLadingCodes);
            if($countBillOfLadingCodes > 0) {
                for ($i = 0; $i < $countBillOfLadingCodes; $i++) {
                    if(strpos( $billOfLadingCodes[$i], Shipment::NEW_BILL_OF_LADING_CODE_PREFIX ) !== false) {
                        $billOfLadingCode = str_replace(Shipment::NEW_BILL_OF_LADING_CODE_PREFIX, "", $billOfLadingCodes[$i]);
                        $shipmentItem = new ShipmentItem();
                        $shipmentItem->shipment_code = $shipment->shipment_code;
                        $shipmentItem->bill_of_lading_code = $billOfLadingCode;
                        $shipmentItem->save();
                    } else {
                        $billOfLadingCode = str_replace(Shipment::DELETE_BILL_OF_LADING_CODE_PREFIX, "", $billOfLadingCodes[$i]);
                        ShipmentItem::where('bill_of_lading_code', '=', $billOfLadingCode)->delete();
                    }
                }
            }
            if($oldShipmentStatus!= $shipment->status && $shipment->status == Shipment::STATUS_DONE) {
                /*$shipment->transport_date = date('Y/m/d h:i:s');*/
                /*$wReceivingVN = new WarehouseReceivingVN();
                $wReceivingVN->status = WarehouseReceivingVN::STATUS_NOT_YET_CONFIRM;
                $wReceivingVN->shipment_code = $shipment->shipment_code;
                $wReceivingVN->warehouse_id = $shipment->warehouse_id;
                $wReceivingVN->save();*/
            }
            $shipment->save();
            DB::commit();
            $shipment->load('userCreator','shipmentItem','warehouse','shipmentItem.billOfLading','shipmentItem.billOfLading.ladingCode');


            return $this->respondSuccessData($shipment, 'Cập nhật thành công');
        }catch (\Exception $ex){
            throw $ex;
            DB::rollBack();
        }

    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id)
    {
        try{
            DB::beginTransaction();
            $shipment = Shipment::findOrFail($id);
            ShipmentItem::where('shipment_code', '=', $shipment->shipment_code)->delete();
            $shipment->delete();
            DB::commit();
            return $this->respondSuccessData([], 'Xóa lô hàng thành công');
        } catch (\Exception $ex){
            DB::rollBack();
        }


    }

    public function getBillOfLadingInfo($id){
        if(ShipmentItem::where('bill_of_lading_code','=',$id)->exists()){
            $shipmentItem = ShipmentItem::where('bill_of_lading_code','=',$id)
                ->first();
            return $this->respondSuccessData([],"Vận đơn này đã được nhập vào lô hàng ". $shipmentItem ->shipment_code);
        }

        if(!WarehouseReceivingCN::where('bill_of_lading_code', '=', $id)->exists()){
            return $this->respondSuccessData([],"Vận đơn này đã chưa được nhập vào kho Trung Quốc ");
        }
        $warehouseCN = WarehouseReceivingCN::where('bill_of_lading_code', '=', $id)->first();
        return $this->respondSuccessData($warehouseCN);
    }
}
