<?php

namespace App\Http\Controllers\Customer;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Modules\BillCode\Models\BillCode;
use Modules\Customer\Models\Customer;
use Modules\Customer\Models\CustomerAddress;
use Modules\CustomerOrder\Models\CustomerOrder;
use Modules\Setting\Models\Setting;
use Modules\Transaction\Models\PaymentInformation;
use Modules\Transaction\Models\Transaction;
use Modules\VerifyLadingCode\Models\SubLadingCode;
use Modules\VerifyLadingCode\Models\VerifyCustomerOrderItem;
use Modules\WarehouseReceivingVN\Models\WarehouseVnLadingItem;

class ReceiveController extends Controller
{
    const gr1 = ['Quận Hoàn Kiếm','Quận Hai Bà Trưng'];
    const gr2 = ['Quận Ba Đình','Quận Đống Đa'];
    const gr3 = ['Quận Thanh Xuân','Quận Cầu Giấy','Quận Hoàng Mai','Quận Long Biên','Quận Tây Hồ'];
    protected $helper;

    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
    }

    public function index(){
        $customerId = Auth::guard('customer')->user()->id;
        $codes = WarehouseVnLadingItem::join('customer_orders','warehouse_vn_lading_items.customer_order_id','=','customer_orders.id')
            ->select(
                'warehouse_vn_lading_items.id',
                'warehouse_vn_lading_items.lading_code',
                'warehouse_vn_lading_items.sub_lading_code',
                'warehouse_vn_lading_items.created_at',
                'customer_orders.id as order_id'
            )
            ->where('customer_orders.customer_id',$customerId)
            ->where('warehouse_vn_lading_items.status',WarehouseVnLadingItem::STATUS_CHECKED)
            ->getQuery()
            ->get();

        return view('customer.order.receive')->with([
            'lists' => $codes,
            'orderDeposit' => $this->helper->countTotalOrderDeposit(),
            'ladingCodePayment' => $codes->count()
        ]);
    }

    public function bill(Request $request){
        $arr = [];
        $addresses = [];
        $orderId = [];
        $orders = [];
        $ladingCodes = collect(json_decode($request->input('ladingCodes')));
        if($ladingCodes->isEmpty()){
            \Session::flash('error','Vui lòng chọn ít nhất 1 kiện hàng');
            return redirect()->back();
        }
        foreach($ladingCodes as &$item){
            $orderId[$item->order_id][] = $item->code;
            $surcharge = 0;
            $discount = 0;
            $customer = Auth::guard('customer')->user();
            $customerAddress = CustomerAddress::where('customer_id',$customer->id)->where('is_default', 1)->first();
            //Số lượng sản phẩm của mã vận đơn
            $quantities = VerifyCustomerOrderItem::with('customerOrderItem')->where('lading_code',$item->code)->select('customer_order_item_id','quantity_verify')->get();
            $quantity_verify = 0;
            foreach($quantities as $quantity){
                $quantity_verify += $quantity->quantity_verify;
                if(!is_null($quantity->customerOrderItem->surcharge))$surcharge += $quantity->customerOrderItem->surcharge;
                if(!is_null($quantity->customerOrderItem->discount_customer_percent)){
                    $discount += $quantity->customerOrderItem->discount_customer_percent * $quantity->customerOrderItem->total_price / 100;
                }elseif(!is_null($quantity->customerOrderItem->discount_customer_price)){
                    $discount += $quantity->customerOrderItem->discount_customer_price;
                }
            }
            $item->quantity_verify = $quantity_verify;
            $weight = 0;

            // Cân nặng của mã vận đơn
            $ladingItem = WarehouseVnLadingItem::find($item->id);
            $weight += $ladingItem->weight;

            $order = CustomerOrder::with('customerOrderItems')->find($item->order_id);
            $item->address = $order->customer_shipping_address;

            if(!in_array($order->customer_shipping_address,$arr)){
                $arr[] = $order->customer_shipping_address;
                $addr['address'] = $order->customer_shipping_address;
                $addr['weight'] = $weight;
                $addr['surcharge'] = $surcharge * $customer->order_rate;
                $addr['discount'] = $discount * $customer->order_rate;
                $addresses[] = $addr;
            }else{
                foreach($addresses as &$address){
                    if($order->customer_shipping_address == $address['address']){
                        $address['weight'] += $weight;
                        $address['surcharge'] += $surcharge;
                        $address['discount'] += $discount;
                    }
                }
            }
        }

        // tính phí ship nội thành theo địa chỉ
        foreach($addresses as &$address){
            if($address['address'] == 'Kho hàng Redex'){
                $address['shipping_fee'] = 0;
            }else if($this->isUrban($address['address'])){
                $address['shipping_fee'] = $this->calculateShippingFee($address);
            }else{
                $address['shipping_fee'] = 'chuyển phát nhanh';
            }
        }

        // tổng số tiền cho order
        foreach($orderId as $id => $item){
            $paymented = 0;
            $totalDeposit = 0;
            $order = CustomerOrder::with('customerOrderItems')->find($id);
            $orderPaymented = PaymentInformation::where('order_id',$id)->where('type',PaymentInformation::TYPE_AMOUNT_ORDER)->get();
            foreach($orderPaymented as $value){
                $data = json_decode($value->data);
                $paymented += $data->pay_amount;
            }
            $transaction = Transaction::where('transactiontable_id',$id)->where('transactiontable_type',CustomerOrder::class)->where('type',Transaction::TYPE_DEPOSIT)->get();
            foreach($transaction as $trans){
                $totalDeposit += $trans->money;
            }
            foreach($order->customerOrderItems as $orderItem){
                $order->total_price += $orderItem->total_price;
            }
            $order->total_deposit = $totalDeposit;
            $order->transport_fee = $this->helper->getTransportFee($id);
            $order->inland_shipping_fee = $this->helper->getInlandShippingFee($id);
            if($paymented > 0){
                $order->pay_amount = $order->transport_fee;
            }else{
                $order->pay_amount = ($order->total_price + $order->inland_shipping_fee) * $customer->order_rate + $order->transport_fee - $order->total_deposit;
            }
            $orders[] = $order;
        }

        return view('customer.lading-code.bill')->with([
            'customer' => $customer,
            'codes' => $ladingCodes,
            'addresses' => $addresses,
            'customerAddress' => $customerAddress,
            'orders' => $orders,
            'totalSurcharge' => $surcharge,
            'totalDiscount' => $discount
        ]);
    }

    public function store(Request $request){
        $data = $request->all();
        $arr = [];

        //tạo giao dịch thanh toán
        $transAttr = [];
        $transAttr['money'] = $data['total_amount'];
        $transAttr['note'] = 'Thanh toán';
        $transAttr['type'] = Transaction::TYPE_PAYMENT;
        $transAttr['customer_id'] = Auth::guard('customer')->user()->id;
        $trans = Transaction::create($transAttr);

        // lưu thông tin địa chỉ
        foreach($data['address'] as $address){
            PaymentInformation::create([
                'transaction_id' => $trans->id,
                'type' => PaymentInformation::TYPE_ADDRESS,
                'data' => json_encode($address),
            ]);
            foreach($address['lading_code'] as $key => $item){
                $arr[$item] = $address['order_id'][$key];
            }
        }

        //lưu thông tin order
        foreach($data['order'] as $order){
            foreach($arr as $key => $item){
                if($item == $order['id']){
                    $order['lading_code'][] = $key;
                }
            }
            PaymentInformation::create([
                'transaction_id' => $trans->id,
                'type' => PaymentInformation::TYPE_AMOUNT_ORDER,
                'data' => json_encode($order),
                'order_id' => $order['id'],
            ]);
        }

        foreach($data['address'] as $address){
            foreach($address['lading_code'] as $code){
                $sub_item = WarehouseVnLadingItem::where('sub_lading_code',$code)->first();
                if ($sub_item){
                    $sub_item->update(['status' => WarehouseVnLadingItem::STATUS_PROCESS_PAYMENT]);
                }else{
                    WarehouseVnLadingItem::where('lading_code',$code)->update(['status' => WarehouseVnLadingItem::STATUS_PROCESS_PAYMENT]);
                }
            }
        }
        \Session::flash('flash_message','Tạo yêu cầu thanh toán thành công. Vui lòng chờ nhân viên CSKH liên hệ lại');
        return redirect(url('customer/receive'));
    }

    protected function isUrban($address){
        $arr = explode(', ',$address);
        if(end($arr) == 'Thành phố Hà Nội'){
            array_pop($arr);
            if(in_array(end($arr),self::gr1) || in_array(end($arr),self::gr2) || in_array(end($arr),self::gr3)){
                return true;
            }
        }
        return false;
    }

    protected function calculateShippingFee($address){
        $weight = $address['weight'];
        $shipping_fee = 0;
        $arr = explode(', ',$address['address']);
        array_pop($arr);
        $isGr1 = $this->isGroup(end($arr),self::gr1);
        $isGr2 = $this->isGroup(end($arr),self::gr2);
        $isGr3 = $this->isGroup(end($arr),self::gr3);

        if ($isGr1) $shipping_fee = $this->shippingFeeGr1($weight);
        if ($isGr2) $shipping_fee = $this->shippingFeeGr2($weight);
        if ($isGr3) $shipping_fee = $this->shippingFeeGr3($weight);

        return $shipping_fee;
    }

    protected function isGroup($element,$arr){
        return in_array($element, $arr) ? true : false;
    }

    protected function shippingFeeGr1($weight){
        if($weight > 0 && $weight <= 5){
            $shipping_fee = 20000;
        }elseif ($weight > 5 && $weight <=10){
            $shipping_fee = 25000;
        }elseif ($weight > 10 && $weight <= 20){
            $shipping_fee = 35000;
        }elseif ($weight > 20 && $weight <= 30){
            $shipping_fee = 50000;
        }elseif ($weight > 30 && $weight <= 40){
            $shipping_fee = 60000;
        }elseif ($weight > 40 && $weight <= 50){
            $shipping_fee = 80000;
        }elseif ($weight > 50 && $weight <= 100){
            $shipping_fee = 100000;
        }

        return $shipping_fee;
    }

    protected function shippingFeeGr2($weight){
        if($weight > 0 && $weight <= 5){
            $shipping_fee = 30000;
        }elseif ($weight > 5 && $weight <=10){
            $shipping_fee = 35000;
        }elseif ($weight > 10 && $weight <= 20){
            $shipping_fee = 45000;
        }elseif ($weight > 20 && $weight <= 30){
            $shipping_fee = 60000;
        }elseif ($weight > 30 && $weight <= 40){
            $shipping_fee = 70000;
        }elseif ($weight > 40 && $weight <= 50){
            $shipping_fee = 100000;
        }elseif ($weight > 50 && $weight <= 100){
            $shipping_fee = 130000;
        }

        return $shipping_fee;
    }

    protected function shippingFeeGr3($weight){
        if($weight > 0 && $weight <= 5){
            $shipping_fee = 40000;
        }elseif ($weight > 5 && $weight <=10){
            $shipping_fee = 45000;
        }elseif ($weight > 10 && $weight <= 20){
            $shipping_fee = 55000;
        }elseif ($weight > 20 && $weight <= 30){
            $shipping_fee = 70000;
        }elseif ($weight > 30 && $weight <= 40){
            $shipping_fee = 80000;
        }elseif ($weight > 40 && $weight <= 50){
            $shipping_fee = 150000;
        }elseif ($weight > 50 && $weight <= 100){
            $shipping_fee = 180000;
        }

        return $shipping_fee;
    }

}
