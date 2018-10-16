<?php

namespace App\Http\Controllers\Customer;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Modules\Withdrawal\Models\Withdrawal;
use Modules\Transaction\Models\Transaction;
use App\Rules\CheckBalance;
use Modules\Customer\Models\CustomerAddress;

class WalletController extends Controller
{
    public function index(Request $request)
    {
        $total_recharge = 0;
        $total_withdrawal = 0;
        $total_refund = 0;
        $customer = Auth::guard('customer')->user();
        $transactions = Transaction::where('customer_id',$customer->id)->paginate(5);
        $totals = Transaction::where('customer_id',$customer->id)->get();
        foreach($totals as $total){
            if($total->type == Transaction::TYPE_WITHDRAWAL){
                $total_withdrawal += $total->money;
            }elseif($total->type == Transaction::TYPE_RECHARGE){
                $total_recharge += $total->money;
            }elseif($total->type == Transaction::TYPE_REFUND){
                $total_refund += $total->money;
            }
        }

        return view('customer.wallet.index')->with([
            'wallet'=>$customer->wallet,
            'transactions' => $transactions,
            'total_withdrawal' => $total_withdrawal,
            'total_recharge' => $total_recharge,
            'total_refund' => $total_refund
        ]);
    }

    public function withdrawal(Request $request){
        $data = $request->all();
        $customer = Auth::guard('customer')->user();
        $balance = $customer->wallet;
        $validator = \Validator::make(
            $data,
            [
                'name' => 'required',
                'bank'     => 'required',
                'account_number' => 'required',
                'money_withdrawal' => ['required', new CheckBalance()],
                'branch' => 'required',
                'content' => 'required'
            ],
            [
                'required' => 'Thông tin bắt buộc',
            ]
        );

        $errors = [];

        if ($validator->fails()) {
            foreach ($validator->messages()->messages() as $att => $messages) {
                $errors[$att] = $messages[0];
            }
            return response()->json([
                'status' => 'invalid',
                'errors' => $errors,
            ]);
        }

        $withdrawal_rq = Withdrawal::create($data);

        $attrTrans = array();
        $attrTrans['transactiontable_id'] = $withdrawal_rq->id;
        $attrTrans['transactiontable_type'] = Withdrawal::class;
        $attrTrans['money'] = $data['money_withdrawal'];
        $attrTrans['note'] = 'rút tiền';
        $attrTrans['type'] = Transaction::TYPE_WITHDRAWAL;
        $attrTrans['customer_id'] = Auth::guard('customer')->user()->id;

        Transaction::create($attrTrans);

        $balance = (float)$balance - (float)$data['money_withdrawal'];
        $customer->update(['wallet' => $balance]);

        return response()->json([
            'status' => 'success',
            'message' => 'Gửi yêu cầu rút tiền thành công.'
        ]);
    }

    public function recharge(){
        $customer = Auth::guard('customer')->user();
        $phone = CustomerAddress::where('customer_id', $customer->id)->where('is_default', 1)->pluck('phone')->first();
        return view('customer.wallet.recharge')->with([
            'customer' => $customer,
            'phone' => $phone
        ]);
    }
}
