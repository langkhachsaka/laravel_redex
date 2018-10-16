<?php
namespace App\Helpers;

use Auth;
use Modules\BillCode\Models\BillCode;
use Modules\Customer\Models\Customer;
use Modules\CustomerOrder\Models\CustomerOrder;
use Modules\CustomerOrder\Models\CustomerOrderItem;
use Modules\Delivery\Models\Delivery;
use Modules\LadingCode\Models\LadingCode;
use Modules\PriceList\Models\PriceList;
use Modules\Setting\Models\Setting;
use Modules\Transaction\Models\PaymentInformation;
use Modules\VerifyLadingCode\Models\SubLadingCode;
use Modules\WarehouseReceivingVN\Models\WarehouseVnLadingItem;

class Helper
{
    /*
     * Tổng số kiện hàng
     */
    public function getTotalLadingCodeByOrder($id){
        $builder = WarehouseVnLadingItem::where('customer_order_id',$id)
            ->get();
        return $builder->count();
    }

    public function getCustomerRate($customer_order_id){
        $customerOrder = CustomerOrder::find($customer_order_id);
        $customer = Customer::find($customerOrder->customer_id);
        return $customer->rate;
    }

    /*
     * Số kiện hàng chờ phát
     */
    public function getTotalPackageWaitingForTranfer($id){
        $builder = WarehouseVnLadingItem::where('customer_order_id',$id)
            ->where('status',WarehouseVnLadingItem::STATUS_WAIT_TRANFER)
            ->get();

        return $builder->count();
    }

    /*
     * Số kiện hàng đã được phát
     */
    public function getTotalPackageTranfered($id){
        $builder = WarehouseVnLadingItem::where('customer_order_id',$id)
            ->where('status',WarehouseVnLadingItem::STATUS_TRANFERED)
            ->get();

        return $builder->count();
    }

    /*
     * Số kiện hàng khiếu nại
     */
    public function getTotalPackageComplaint($id){
        $builder = WarehouseVnLadingItem::where('customer_order_id',$id)
            ->where('status',WarehouseVnLadingItem::STATUS_ERROR)
            ->get();
        return $builder->count();
    }

    /*
     * tính phí vận chuyển trung quốc-việt nam
     */
    public function getTransportFee($id){
        $arr = [];
        $isCusWholeSale = $this->isCustomerWholse();
        $feePaymented = 0;
        $feeFast = 0;
        $feeNormal = 0;
        $weightNormal = 0;
        $weightFast = 0;

        // số tiền ship trung quốc-vn đã thanh toán
        $orderPaymented = PaymentInformation::where('order_id',$id)->where('type',PaymentInformation::TYPE_AMOUNT_ORDER)->get();
        foreach($orderPaymented as $item){
            $data = json_decode($item->data);
            $feePaymented += $data->transport_fee;

            //lấy ra các mã vận đơn đã trả tiền ship
            $arr = $data->lading_code;
        }

        foreach($arr as $code){
            $weightConvert = 0;
            $item = WarehouseVnLadingItem::where('sub_lading_code',$code)->first();
            if(!$item){
                $item = WarehouseVnLadingItem::where('lading_code',$code)->first();
            }

            $deliveryType = BillCode::join('lading_codes', 'bill_codes.bill_code', '=', 'lading_codes.bill_code')
                ->select([
                    'bill_codes.delivery_type'
                ])
                ->where('lading_codes.code',$item->lading_code)
                ->first();
            if(Setting::getValue('rate') != 0){
                $weightConvert = $item->height * $item->width * $item->length / Setting::getValue('rate');
            }

            if($item->weight < $weightConvert){
                $weight = $weightConvert;
            }else{
                $weight = $item->weight;
            }

            if($deliveryType->delivery_type == BillCode::CONST_1){
                $weightNormal += $weight;
            }else{
                $weightFast += $weight;
            }
        }
        $items = WarehouseVnLadingItem::where('warehouse_vn_lading_items.customer_order_id',$id)
            ->where('warehouse_vn_lading_items.status',WarehouseVnLadingItem::STATUS_CHECKED)
            ->get();

        foreach ($items as $item){
            $weightConvert = 0;

            if(Setting::getValue('rate') != 0){
                $weightConvert = $item->height * $item->width * $item->length / Setting::getValue('rate');
            }

            if($item->weight < $weightConvert){
                $weight = $weightConvert;
            }else{
                $weight = $item->weight;
            }
            $billCode = LadingCode::where('code',$item->lading_code)->pluck('bill_code')->first();
            $billCodeItem = BillCode::where('bill_code',$billCode)->where('customer_order_id',$id)->first();

            if($billCodeItem->delivery_type == BillCode::CONST_1){
                $weightNormal += $weight;
            }else{
                $weightFast += $weight;
            }
        }

        // tính phí chuyển nhanh
        if($isCusWholeSale){
            if($weightFast > 0 && $weightFast < 30)$feeFast = PriceList::where('key','less_than_30_is_wholesale')->pluck('price')->first() * $weightFast;
            if($weightFast >= 30)$feeFast = PriceList::where('key','more_than_30_is_wholesale')->pluck('price')->first() * $weightFast;
        }else{
            if($weightFast < 0.5 && $weightFast > 0)$feeFast = PriceList::where('key','less_than_half')->pluck('price')->first();
            if($weightFast >= 0.5 && $weightFast < 5)$feeFast = PriceList::where('key','less_than_half')->pluck('price')->first() + ($weightFast - 0.5)*PriceList::where('key','more_than_half')->pluck('price')->first();
            if($weightFast >= 5 && $weightFast < 30)$feeFast = PriceList::where('key','more_than_5')->pluck('price')->first() * $weightFast;
            if($weightFast >= 30)$feeFast = PriceList::where('key','more_than_30')->pluck('price')->first() * $weightFast;
        }

        // tính phí chuyển thường
        if($weightNormal > 0 && $weightNormal < 30) $feeNormal = PriceList::where('key','less_than_30_normal')->pluck('price')->first() * $weightNormal;
        if($weightNormal >= 30) $feeNormal = PriceList::where('key','more_than_30_normal')->pluck('price')->first() * $weightNormal;
        $fee = $feeFast + $feeNormal - $feePaymented;

        return $fee;
    }

    /*
     * Kiem tra customer la khach si or khach le
     */
    public function isCustomerWholse(){
        $totalWeight = 0;
        $today = date("Y-m-d");
        $threeMonthAgo = date("Y-m-d", strtotime("$today -3 month"));
        $customerId = Auth::guard('customer')->user()->id;

        $orders = CustomerOrder::where('customer_id',$customerId)
            ->whereDate(\DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d")'),'>=', $threeMonthAgo)
            ->whereDate(\DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d")'),'<=', $today)
            ->get();
        foreach($orders as $order){
            $builder = \DB::table('bill_codes')
                ->join('lading_codes', 'bill_codes.bill_code', '=', 'lading_codes.bill_code')
                ->select([
                    'lading_codes.code',
                ])
                ->where('bill_codes.customer_order_id',$order->id)
                ->get();
            foreach($builder as $item){
                $sql = \DB::table('warehouse_vn_lading_items')->select('weight')->where('lading_code',$item->code)->first();
                if($sql)$totalWeight += $sql->weight;
            }
        }

        return $totalWeight >= 200 ? true : false;
    }

    public function getInlandShippingFee($id){
        $fee = 0;
        $billCodes = BillCode::where('customer_order_id',$id)->get();
        foreach($billCodes as $billCode){
            $fee += $billCode->fee_ship_inland;
        }

        return $fee;
    }

    public function countTotalOrderDeposit(){
        $customerId = Auth::guard('customer')->user()->id;
        $listOrderDeposit = CustomerOrder::with('customerOrderItems','seller')->where('customer_id',$customerId)->where('status', 1)->get();
        if($listOrderDeposit){$count = count($listOrderDeposit);}
        return $count ? $count : 0;
    }
    public function getCustomerIdFromCustomerOrderItemId($customerOrderId){
        $customerOrder = CustomerOrderItem::with('customerOrder')->where('id',$customerOrderId)->first();
        return $customerOrder['customerOrder']['customer_id'];
    }

    public function countLadingCodePayment(){
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

        return $codes->count();
    }

}