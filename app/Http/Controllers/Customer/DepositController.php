<?php

namespace App\Http\Controllers\Customer;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Modules\CustomerOrder\Models\CustomerOrder;
use Modules\VerifyLadingCode\Models\VerifyLadingCode;

class DepositController extends Controller
{
    protected $helper;

    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
    }

    public function index(){
        $customer = Auth::guard('customer')->user();
        $listOrder = CustomerOrder::with('customerOrderItems','seller')->where('customer_id', $customer->id)->where('status', 1)->paginate(10);
        foreach ($listOrder as &$order)
        {
            foreach($order->customerOrderItems as $orderItem){
                $order->total += $orderItem->total_price;
            }
            $order->total_deposit = $order->total * $customer->order_rate * $customer->order_pre_deposit_percent / 100;
            $order->total_lading_codes = $this->helper->getTotalLadingCodeByOrder($order->id);
            $order->package_tranfered = $this->helper->getTotalPackageTranfered($order->id);
            $order->package_wait_to_tranfer = $this->helper->getTotalPackageWaitingForTranfer($order->id);
            $order->package_need_pay = $order->package_wait_to_tranfer;
            $order->package_complaint = $this->helper->getTotalPackageComplaint($order->id);
        }
        return view('customer.order.deposit')->with([
            'list' => $listOrder,
            'customer' => $customer,
            'orderDeposit' => $this->helper->countTotalOrderDeposit(),
            'ladingCodePayment' => $this->helper->countLadingCodePayment()
        ]);
    }

}
