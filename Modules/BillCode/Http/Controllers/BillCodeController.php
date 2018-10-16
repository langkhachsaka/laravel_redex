<?php

namespace Modules\BillCode\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Base\Http\Controllers\Controller;
use Modules\BillCode\Models\BillCode;
use Modules\CustomerOrder\Models\CustomerOrder;

class BillCodeController extends Controller
{
    public function show($id)
    {
        $billCode = BillCode::findOrFail($id);

        return $this->respondSuccessData($billCode);
    }

    public function store(Request $request)
    {
        /** @var BillCode $billCode */
        $billCode = BillCode::filterWhere('customer_order_id', '=', $request->input('customer_order_id'))
            ->filterWhere('shop_id', '=', $request->input('shop_id'))
            ->first();

        if (is_null($billCode)) {
            $billCode = new BillCode();
        }

        $billCode->fill($request->input());

        $billCode->save();

        $data = CustomerOrder::with(
            'CustomerOrderItems',
            'CustomerOrderItems.images',
            'CustomerOrderItems.shop',
            'CustomerOrderItems.ladingCodes',
            'customer',
            'seller',
            'customer.customerAddresses',
            'billCodes',
            'ladingCodes'
        )->findOrFail($billCode->customer_order_id);

        return $this->respondSuccessData($data, 'Bạn đã thêm mới mã hóa đơn thành công');
    }

    public function update(Request $request, $id)
    {
        /** @var BillCode $billCode */
        $billCode = BillCode::findOrFail($id);

        $billCode->fill($request->input());

        $billCode->save();

        $data = CustomerOrder::with(
            'CustomerOrderItems',
            'CustomerOrderItems.images',
            'CustomerOrderItems.shop',
            'CustomerOrderItems.ladingCodes',
            'customer',
            'seller',
            'customer.customerAddresses',
            'billCodes',
            'ladingCodes'
        )->findOrFail($billCode->customer_order_id);

        return $this->respondSuccessData($data, 'Bạn đã sửa mã hóa đơn thành công');
    }
}
