<?php

namespace Modules\WarehouseReceivingCN\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Base\Http\Controllers\Controller;
use Modules\LadingCode\Models\LadingCode;
use Modules\WarehouseReceivingCN\Models\WarehouseReceivingCN;
use Maatwebsite\Excel\Facades\Excel;

class WarehouseReceivingCNController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user_receive_id = auth()->user()->isChineseShippingOfficer()
            ? auth()->id()
            : $request->input('user_receive_id');

        $perPage = $this->getPerPage($request);
        $wReceiving = WarehouseReceivingCN::with('warehouse','userReceive','shipmentItem','shipmentItem.shipment','ladingCode','ladingCode.billOfLading',
            'ladingCode.billOfLading.customer',
            'ladingCode.billCode','ladingCode.billCode.customerOrder','ladingCode.billCode.customerOrder.customer')
                ->WhereFullLike('bill_of_lading_code', $request->input('bill_of_lading_code'))
                ->filterWhere('date_receiving', '>=', $request->input('date_receiving_from'))
                ->filterWhere('date_receiving', '<=', $request->input('date_receiving_to'))
                ->filterWhere('warehouse_id', '=', $request->input('warehouse_id'))
               /* ->filterWhere('user_receive_id', '=', $user_receive_id)*/
               ->filterWhere('status', '=', $request->input('status'))
                ->orderBy('date_receiving', 'desc')
                ->paginate($perPage);

        return $this->respondSuccessData($wReceiving);
    }

    /**
     *  get status
     * @param $billOfLadingCode
     * @return int
     */
    private function getStatus($billOfLadingCode)
    {
        if(LadingCode::where('code', '=', $billOfLadingCode)->exists()) {
            return WarehouseReceivingCN::STATUS_MATCHED;
        } else {
            return WarehouseReceivingCN::STATUS_UNMATCHED;
        }
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $wReceiving = new WarehouseReceivingCN();
        if($this->checkExistLadingCodeInWarehouseCN($request->bill_of_lading_code)){
            return $this->respondSuccessData([], 'error');
        } else {
            $requestData = $request->all();
            $validator = $this->validateRequestData($requestData);

            if ($validator->fails()) {
                return $this->respondInvalidData($validator->messages());
            }
            $wReceiving->fill($requestData);
            $wReceiving->status = $this->getStatus($wReceiving->bill_of_lading_code);
            $wReceiving->save();
            $wReceiving->load('warehouse','userReceive','shipmentItem','shipmentItem.shipment','ladingCode','ladingCode.billOfLading',
                'ladingCode.billOfLading.customer',
                'ladingCode.billCode','ladingCode.billCode.customerOrder','ladingCode.billCode.customerOrder.customer');
            return $this->respondSuccessData($wReceiving, 'Thêm mới thành công');
        }

    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $wReceiving = WarehouseReceivingCN::findOrFail($id);
        return $this->respondSuccessData($wReceiving);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $wReceiving = WarehouseReceivingCN::findOrFail($id);
        $oldBillOfLadingCode = $wReceiving->bill_of_lading_code;
        if($oldBillOfLadingCode != $request->bill_of_lading_code && $this->checkExistLadingCodeInWarehouseCN($request->bill_of_lading_code)){
            return $this->respondSuccessData([], 'error');
        } else {


            $requestData = $request->all();
            $validator = $this->validateRequestData($requestData);
            if ($validator->fails()) {
                return $this->respondInvalidData($validator->messages());
            }
            $wReceiving->fill($requestData);

            $wReceiving->status = $this->getStatus($wReceiving->bill_of_lading_code);
            $wReceiving->save();
            $wReceiving->load('warehouse','userReceive','shipmentItem','shipmentItem.shipment','ladingCode','ladingCode.billOfLading',
                'ladingCode.billOfLading.customer',
                'ladingCode.billCode','ladingCode.billCode.customerOrder','ladingCode.billCode.customerOrder.customer');
            return $this->respondSuccessData($wReceiving, 'Cập nhật thành công');
        }
    }

    public function importFileExcel(Request $request){
        $userReceiveId = auth()->user()->id;
        $warehouseId = auth()->user()->warehouse_id;
        try {
            DB::beginTransaction();
            $file = $request->file('file');
            $warehouseCNs = [];
            $errorFields = [];
            $results = collect([]);

            Excel::load($file, function ($reader) use (&$results) {
                $results = $reader->get();
            });
            foreach ($results as $key => $row) {
                if (!is_array($row)) {
                    $items = $row->toArray();
                } else {
                    $items = $row;
                }
                if($key == 0 && !isset($items[0]['ma_van_don'])){
                       return $this->respondSuccessData([],'Cấu trúc file Excel không đúng. Vui lòng kiểm tra lại');
                }
                for ($i = 0; $i < count($items); $i++ ){
                    if($this->checkExistLadingCodeInWarehouseCN($items[$i]['ma_van_don'])){
                        array_push($errorFields,['lading_code' =>$items[$i]['ma_van_don'],'row_number' =>$i + 1]);
                    } else {
                        $warehouseCN = new WarehouseReceivingCN();
                        $warehouseCN->bill_of_lading_code = $items[$i]['ma_van_don'];
                        $warehouseCN->weight = $items[$i]['khoi_luong'];
                        $warehouseCN->status = $this->getStatus($warehouseCN->bill_of_lading_code);
                        $warehouseCN->user_receive_id = $userReceiveId;
                        $warehouseCN->date_receiving = date('Y/m/d h:i:s');
                        $warehouseCN->warehouse_id = $warehouseId;
                        $warehouseCN->save();
                        $warehouseCN->load('warehouse','userReceive','shipmentItem','shipmentItem.shipment','ladingCode','ladingCode.billOfLading',
                            'ladingCode.billOfLading.customer',
                            'ladingCode.billCode','ladingCode.billCode.customerOrder','ladingCode.billCode.customerOrder.customer');
                        array_push($warehouseCNs, $warehouseCN);
                    }

                }
            }
            if(count($errorFields) > 0 ){
                DB::rollBack();
                return $this->respondSuccessData($errorFields,'error');
            } else {
                DB::commit();
                return $this->respondSuccessData($warehouseCNs,'Thêm kiện hàng từ file thành công');
            }
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }

    }

    private function checkExistLadingCodeInWarehouseCN($lading_code){
        return WarehouseReceivingCN::where('bill_of_lading_code','=',$lading_code)->exists();
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        /** @var WarehouseReceivingCN $wReceiving */
        $wReceiving = WarehouseReceivingCN::findOrFail($id);

        $wReceiving->delete();

        return $this->respondSuccessData([], 'Xóa thành công');
    }

    /**
     * @param $requestData
     * @return \Illuminate\Validation\Validator
     */
    private function validateRequestData($requestData)
    {
        return \Validator::make(
            $requestData,
            [
                'bill_of_lading_code' => 'required',
                'date_receiving' => 'required',

            ],
            [
                'bill_of_lading_code.required' => 'Chưa nhập mã vận đơn',
                'date_receiving.required' => 'Chưa nhập ngày nhận hàng',
            ]
        );
    }
}
