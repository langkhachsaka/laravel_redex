<?php

namespace Modules\Transaction\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Base\Http\Controllers\Controller;
use Modules\BillOfLading\Models\BillOfLading;
use Modules\CustomerOrder\Models\CustomerOrder;
use Modules\Task\Models\CustomerOrderTask;
use Modules\Transaction\Models\PaymentInformation;
use Modules\Transaction\Models\Transaction;
use Modules\WarehouseReceivingVN\Models\WarehouseVnLadingItem;

class TransactionController extends Controller
{


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorize('index', Transaction::class);

        $perPage = $this->getPerPage($request);
        $transactions = Transaction::with('transactiontable')
            ->filterWhere('type', '=', $request->input('type'))
            ->filterWhere('transactiontable_type', '=', $request->input('transactiontable_type'))
            ->filterWhere('transactiontable_id', '=', $request->input('transactiontable_id'))
            ->orderBy('transactions.id', 'desc')
            ->paginate($perPage);

        return $this->respondSuccessData($transactions);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $this->authorize('create', Transaction::class);

            $transaction = new Transaction();

            $requestData = $request->input();

            $validate = $this->validateRequestData($requestData);
            if ($validate->fails()) {
                return $this->respondInvalidData($validate->messages());
            }

            $transaction->fill($requestData);
            $transaction->user_id = auth()->id();
            $transaction->user_name = auth()->user()->name;
            $transaction->status = 0;

            $transaction->save();

            $transaction->transactiontable->status = CustomerOrder::STATUS_DEPOSITED;
            $transaction->transactiontable->save();

            //UPDATE TASK
            CustomerOrderTask::transactionCustomerOrder($transaction);
            DB::commit();
            $transaction->load('transactiontable');

            return $this->respondSuccessData($transaction, 'Thêm mới giao dịch thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show($id)
    {
        $this->authorize('view', Transaction::class);
        /** @var Transaction $transaction */
        $transaction = Transaction::findOrFail($id);

        $order = collect();

        switch ($transaction->transactiontable_type) {
            case CustomerOrder::class :
                $order = CustomerOrder::findOrFail($transaction->transactiontable_id);
                break;
            case BillOfLading::class :
                $order = BillOfLading::findOrFail($transaction->transactiontable_id);
                break;
        }

        $order->load('transactions');

        return $this->respondSuccessData($order);
    }

    private function validateRequestData($requestData)
    {
        return \Validator::make(
            $requestData,
            [
                'transactiontable_id' => 'bail|required',
                'money' => 'bail|required',
                'type' => 'bail|required',
            ],
            [
                'transactiontable_id.required' => 'Chưa nhập đơn hàng',
                'money.required' => 'Chưa nhập số tiền giao dịch',
                'type.unique' => 'Chưa chọn loại giao dịch'
            ]
        );
    }

    public function paymentDetail($id){
        $transaction = Transaction::with('paymentInfo','customer')->findOrFail($id);
        return $this->respondSuccessData($transaction);
    }

    public function updateShippingFee($id, Request $request){
        $data = $request->all();
        $shipping_fee = 0;
        foreach($data as $item){
            $info = PaymentInformation::find($item['id']);
            $infoDc = json_decode($info->data);
            if(isset($infoDc->shipping_fee)){
                $shipping_fee += $item['value'] - $infoDc->shipping_fee;
            }else{
                $shipping_fee += $item['value'];
            }
            $infoDc->shipping_fee = $item['value'];
            $info->update(['data' => json_encode($infoDc)]);
        }

        $transaction = Transaction::with('paymentInfo','customer')->findOrFail($id);
        $transaction->update(['money' => $transaction->money + $shipping_fee]);
        return $this->respondSuccessData($transaction,'Cập nhật thành công');
    }

    public function rechargeDetail($id){
        $transaction = Transaction::with('customer')->findOrFail($id);
        return $this->respondSuccessData($transaction);
    }

    public function paymentConfirm($id){
        $transaction = Transaction::with('paymentInfo')->findOrFail($id);
        foreach($transaction->paymentInfo as $item){
            if($item->type == PaymentInformation::TYPE_ADDRESS){
                $data = json_decode($item->data);
                foreach ($data->lading_code as $code){
                    $ladingItem = WarehouseVnLadingItem::where('sub_lading_code',$code)->first();
                    if($ladingItem){
                        $ladingItem->update(['status' => WarehouseVnLadingItem::STATUS_PAYMENTED]);
                    }else{
                        WarehouseVnLadingItem::where('lading_code',$code)->update(['status' => WarehouseVnLadingItem::STATUS_PAYMENTED]);
                    }
                }
            }
        }
        $transaction->update(['status' => 1]);

        return $this->respondSuccessData($transaction,'Xác nhận thành công');
    }

    public function depositConfirm($id){
        $transaction = Transaction::findOrFail($id);
        $transaction->update(['status' => 1]);

        $order = collect();
        $order = CustomerOrder::findOrFail($transaction->transactiontable_id);
        $order->update(['status' => CustomerOrder::STATUS_DEPOSITED]);
        $order->load('transactions');

        return $this->respondSuccessData($order,'Xác nhận thành công');
    }
}
