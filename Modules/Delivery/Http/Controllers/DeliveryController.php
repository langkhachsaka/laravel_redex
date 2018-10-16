<?php

namespace Modules\Delivery\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Base\Http\Controllers\Controller;
use Modules\Delivery\Models\Delivery;
use Modules\Task\Models\DeliveryTask;
use Modules\Transaction\Models\Transaction;
use Modules\WarehouseReceivingVN\Models\WarehouseVnLadingItem;

class DeliveryController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorize('index', Delivery::class);

        $perPage = $this->getPerPage($request);

        /*$sipperId = auth()->user()->isVietnameseShippingOfficer() || auth()->user()->isChineseShippingOfficer()
            ? auth()->id()
            : null;*/

        $deliveries = Transaction::with('delivery','delivery.user','paymentInfo','customer')
            ->where('type',Transaction::TYPE_PAYMENT)->where('status', Transaction::STT_CONFIRMED)
            ->paginate($perPage);

        return $this->respondSuccessData($deliveries);
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request)
    {
        $this->authorize('create', Delivery::class);

        $requestData = $request->all();

        $validator = $this->validateRequestData($requestData);
        if ($validator->fails()) {
            return $this->respondInvalidData($validator->messages());
        }

        $delivery = new Delivery();

        $delivery->fill($requestData);
        $delivery->save();

        $delivery->load('user');
        return $this->respondSuccessData($delivery, 'Thêm đơn hàng xuất thành công');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * Creaete task delivery
     */
    public function createTaskDelivery(Request $request)
    {

        try{
            DB::beginTransaction();
            $user_id = $request->user_id;
            $transactionDetails = $request->transactionDetail;
            $transactionIds = [];
            foreach ($transactionDetails as $transactionDetail){
                //CREATE ITEM IN TABLE DELIVERY
                $delivery = new Delivery();
                $delivery->user_id = $user_id;
                $delivery->status = Delivery::STATUS_PENDING;
                $delivery->transaction_id = $transactionDetail['id'];
                $delivery->save();

                //CREATE TASK FOR DELIVERY
                DeliveryTask::newTaskDelivery($transactionDetail,$user_id);

                array_push($transactionIds,$transactionDetail['id']);
            }
            $deliveries = Transaction::with('delivery','delivery.user','paymentInfo','customer')->whereIn('id',$transactionIds)->get();
            DB::commit();
            return $this->respondSuccessData($deliveries, 'Tạo nhiệm vụ xuất hàng thành công');
        }catch (\Exception $e){
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Show the specified resource.
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show($id)
    {
        $this->authorize('view', Delivery::class);

        /** @var Delivery $delivery */
        $delivery = Transaction::with('delivery','delivery.user','paymentInfo','customer')
            ->where('type',Transaction::TYPE_PAYMENT)->where('id', $id)
            ->first();

        return $this->respondSuccessData($delivery);
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, $id)
    {
        $this->authorize('update', Delivery::class);

        $requestData = $request->all();

        $validator = $this->validateRequestData($requestData, $id);
        if ($validator->fails()) {
            return $this->respondInvalidData($validator->messages());
        }

        /** @var Delivery $delivery */
        $delivery = Delivery::findOrFail($id);

        $delivery->fill($requestData);
        $delivery->save();

        $delivery->load('user');
        return $this->respondSuccessData($delivery, 'Sửa đơn hàng xuất thành công');
    }

    /**
     * Remove the specified resource from storage.
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $this->authorize('delete', Delivery::class);

        /** @var Delivery $delivery */
        $delivery = Delivery::findOrFail($id);

        $delivery->delete();

        return $this->respondSuccessData([], 'Xóa đơn hàng xuất thành công');
    }

    private function validateRequestData($requestData, $modelID = 0)
    {
        return \Validator::make(
            $requestData,
            [
                'lading_code' => 'bail|required|string|max:255|unique:deliveries,lading_code,' . $modelID,
            ],
            [
                'lading_code.required' => 'Chưa nhập mã vận đơn',
                'lading_code.max' => 'Mã vận đơn dài tối đa 255 ký tự',
                'lading_code.unique' => 'Mã vận đơn đã được sử dụng'
            ]
        );
    }

    public function confirm(Request $request,$id){
        $input = $request->all();

        //upload image
        $file = $input['file'];
        $path = $file->store('report/'.date('Y/m/d'));

        $transaction = Transaction::with('delivery','paymentInfo')->findOrFail($id);
        $attr = array();
        $attr['status'] = Delivery::STATUS_PROCESSED;
        $attr['date_delivery'] = $input['date'];
        $attr['image'] = $path;

        Delivery::where('id',$transaction->delivery->id)->update($attr);
        foreach($transaction->paymentInfo as $item){
            if($item['type'] == 0){
                $data = json_decode($item['data']);
                foreach($data->lading_code as $code){
                    $ladingItem = WarehouseVnLadingItem::where('sub_lading_code',$code)->first();
                    if($ladingItem){
                        $ladingItem->update(['status' => WarehouseVnLadingItem::STATUS_TRANFERED]);
                    }else{
                        WarehouseVnLadingItem::where('lading_code',$code)->update(['status' => WarehouseVnLadingItem::STATUS_TRANFERED]);
                    }
                }
            }
        }
        $transaction->load('delivery','delivery.user','paymentInfo','customer');

        //Update Delivery Task
        DeliveryTask::confirmDelivery($id);

        return $this->respondSuccessData($transaction, 'Xác nhận thành công');
    }
}
