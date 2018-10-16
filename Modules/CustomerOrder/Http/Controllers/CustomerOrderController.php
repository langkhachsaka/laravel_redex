<?php

namespace Modules\CustomerOrder\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Base\Http\Controllers\Controller;
use Modules\BillCode\Models\BillCode;
use Modules\Customer\Models\CustomerAddress;
use Modules\CustomerOrder\Models\CustomerOrder;
use Modules\CustomerOrder\Models\CustomerOrderItem;
use Modules\Image\Models\Image;
use Modules\Notification\Models\CustomerOrderNotification;
use Modules\Rate\Models\Rate;
use Modules\Setting\Models\Setting;
use Modules\Task\Models\CustomerOrderTask;

class CustomerOrderController extends Controller
{

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorize('index', CustomerOrder::class);

        $perPage = $this->getPerPage($request);

        /** Customer Service Officer can view only their Orders */
        $seller_id = auth()->user()->isCustomerServiceOfficer()
            ? auth()->id()
            : $request->input('seller_id');

        /** Accountant can view only approved Orders */
        $customer_order_status = auth()->user()->isAccountant()
            ? CustomerOrder::STATUS_APPROVED
            : $request->input('status');

        $customerOrders = CustomerOrder::with(
            'customer',
            'customer.customerAddresses',
            'seller',
            'customerOrderItems'
        )
            ->filterWhere('status', '=', $customer_order_status)
            ->filterWhere('customer_orders.created_at', '>=', $request->input('created_at_from'))
            ->filterWhere('customer_orders.created_at', '<=', $request->input('created_at_to'))
            ->filterWhere('customer_orders.customer_id', '=', $request->input('customer_id'))
            ->filterWhere('customer_orders.seller_id', '=', $seller_id)
            ->orderBy('customer_orders.id', 'desc')
            ->paginate($perPage);

        return $this->respondSuccessData($customerOrders);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function store(Request $request)
    {
        $this->authorize('create', CustomerOrder::class);

        try {
            DB::beginTransaction();
            $cusOrder = new CustomerOrder(['status' => CustomerOrder::STATUS_PENDING]);
            $cusOrder->fill($request->input());

            /**
             * When Customer Service Officer create new Orders
             *  Get their ID put to seller_id of Orders
             */
            auth()->user()->isCustomerServiceOfficer()
                ? $cusOrder->seller_id = auth()->id()
                : $cusOrder->seller_id = $request->input('seller_id');


            // fill shipping address to order table
            $customerAddress = CustomerAddress::find($cusOrder->customer_shipping_address_id);
            if ($customerAddress) {
                $cusOrder->fill([
                    'customer_shipping_name' => $customerAddress->name,
                    'customer_shipping_phone' => $customerAddress->phone,
                    'customer_shipping_address' => $customerAddress->address,
                    'customer_shipping_provincial_id' => $customerAddress->provincial_id,
                    'customer_shipping_district_id' => $customerAddress->district_id,
                    'customer_shipping_ward_id' => $customerAddress->ward_id,
                ]);
            }

            $cusOrder->save();

            collect($request->input('customer_order_items', []))
                ->each(function ($item) use ($cusOrder) {

                    $cusOrderItem = new CustomerOrderItem(['status' => CustomerOrderItem::STATUS_PENDING]);
                    $cusOrderItem->fill($item);
                    $cusOrderItem->customerOrder()->associate($cusOrder);
                    $cusOrderItem->save();

                    foreach ($item['images'] as $img) {
                        $image = new Image();
                        $image->path = $img;
                        $image->imagetable_id = $cusOrderItem->id;
                        $image->imagetable_type = CustomerOrderItem::class;
                        $image->save();
                    }
                });


            // BEGIN AUTO CREATE TASK
            CustomerOrderTask::newCustomerOrderByUser($cusOrder);
            // END PROCESS AUTO CREATE TASK


            // BEGIN CREATE NOTIFICATION
            CustomerOrderNotification::newCustomerOrderByUser($cusOrder->id, auth()->id());
            CustomerOrderNotification::assignCustomerOrder(
                $cusOrder->id,
                $cusOrder->seller_id,
                auth()->user()->isCustomerServiceOfficer() ? null : auth()->id()
            );
            // END

            DB::commit();

            $cusOrder->load('CustomerOrderItems', 'customer', 'seller', 'customer.customerAddresses');
            return $this->respondSuccessData($cusOrder, 'Tạo đơn hàng thành công');
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show($id)
    {
        /** @var CustomerOrder $model */
        $model = CustomerOrder::with([
            'CustomerOrderItems',
            'CustomerOrderItems.images',
            'CustomerOrderItems.shop',
            'seller',
            'customer',
            'customer.customerAddresses',
            'billCodes',
            'billCodes.ladingCodes',
        ])->findOrFail($id);

        $this->authorize('view', $model);

        // fill `money_exchange_rate`, `deposit_percent` if not filled
        if (empty($model->money_exchange_rate)) {
            $model->money_exchange_rate = $model->customer ? $model->customer->order_rate : Rate::lastOrderRate();
        }
        if (empty($model->deposit_percent)) {
            $model->deposit_percent = $model->customer ? $model->customer->order_pre_deposit_percent : Setting::getValue('order_deposit_percent');
        }

        return $this->respondSuccessData($model);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function approve($id)
    {
        $this->authorize('approve', CustomerOrder::class);

        DB::beginTransaction();
        try {
            /** @var CustomerOrder $cusOrder */
            $cusOrder = CustomerOrder::findOrFail($id);
            $cusOrder->status = CustomerOrder::STATUS_APPROVED;
            $cusOrder->save();

            CustomerOrderItem::where('customer_order_id', $id)
                ->update(['customer_order_items.status' => CustomerOrderItem::STATUS_APPROVED]);

            // BEGIN AUTO UPDATE TASK
            CustomerOrderTask::approveCustomerOrder($cusOrder);
            // END PROCESS AUTO UPDATE TASK

            //BEGIN NOTIFICATION
            CustomerOrderNotification::approveCustomerOrder($cusOrder->id, $cusOrder->seller_id, auth()->id());
            //END
            DB::commit();

            $cusOrder->load([
                'CustomerOrderItems',
                'CustomerOrderItems.images',
                'CustomerOrderItems.shop',
                'seller',
                'customer',
                'customer.customerAddresses',
                'billCodes',
                'billCodes.ladingCodes',
            ]);

            return $this->respondSuccessData($cusOrder, 'Duyệt đơn hàng thành công');
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            /** @var CustomerOrder $cusOrder */
            $cusOrder = CustomerOrder::findOrFail($id);
            $oldSellerId = $cusOrder->seller_id;
            $oldCustomerShippingName = $cusOrder->customer_shipping_name;
            $oldCustomerShippingAddress = $cusOrder-> customer_shipping_address;
            $oldCustomerShippingPhone = $cusOrder-> customer_shipping_phone;
            $this->authorize('update', $cusOrder);

            $requestData = $request->all();

            $cusOrder->fill($requestData);



            // fill shipping address to order table
            $customerAddress = CustomerAddress::with('provincial','district','ward')->find($cusOrder->customer_shipping_address_id);
            if ($customerAddress) {
                $cusOrder->fill([
                    'customer_shipping_name' => $customerAddress->name,
                    'customer_shipping_phone' => $customerAddress->phone,
                    'customer_shipping_address' => $customerAddress->address.', '.$customerAddress->ward->name.', '.$customerAddress->district->name.', '.$customerAddress->provincial->name,
                    'customer_shipping_provincial_id' => $customerAddress->provincial_id,
                    'customer_shipping_district_id' => $customerAddress->district_id,
                    'customer_shipping_ward_id' => $customerAddress->ward_id,
                ]);
            }


            $cusOrder->save();


            $itemsIdInOrder = []; // save list id in order, then will delete other item not in this list
            collect($request->input('customer_order_items', []))
                ->each(function ($item) use ($cusOrder, &$itemsIdInOrder) {

                    /** @var CustomerOrderItem $cusOrderItem */
                    if (!empty($item['id'])) {
                        $cusOrderItem = CustomerOrderItem::find($item['id']);
                    }

                    if (!isset($cusOrderItem)) {
                        // new item when update order
                        $cusOrderItem = new CustomerOrderItem([
                            'customer_order_id' => $cusOrder->id,
                            'status' => CustomerOrderItem::STATUS_PENDING,
                        ]);
                    }

                    $cusOrderItem->fill($item);
                    $cusOrderItem->save();

                    // add to list
                    $itemsIdInOrder[] = $cusOrderItem->id;

                    // delete all images then insert again
                    $cusOrderItem->images()->delete();

                    foreach ($item['images'] as $img) {
                        $image = new Image();
                        $image->path = $img;
                        $image->imagetable_id = $cusOrderItem->id;
                        $image->imagetable_type = CustomerOrderItem::class;
                        $image->save();
                    }
                });

            // delete item not in list
            $cusOrder->customerOrderItems()->whereNotIn('id', $itemsIdInOrder)->delete();



            // update delivery_type, insurance_type, reinforced_type, fee_ship_inland
            foreach ($request->input('shop_bill_codes', []) as $shopId => $billCodeAttr) {
                if (!$shopId || empty($billCodeAttr)) continue;
                /** @var BillCode $billCode */
                $billCode = BillCode::firstOrNew([
                    'customer_order_id' => $cusOrder->id,
                    'shop_id' => $shopId,
                ]);
                $billCode->fill($billCodeAttr);
                $billCode->save();
            }



            // CHECK CHANGE STATUS AND PERFORMER_ID AND CHANGE IN TASKS
            CustomerOrderTask::updateCustomerOrderByUser($cusOrder, $oldSellerId, $oldCustomerShippingName, $oldCustomerShippingAddress, $oldCustomerShippingPhone);
            // END PROCESS

            if ($oldSellerId != $cusOrder->seller_id) {
                CustomerOrderNotification::assignCustomerOrder($cusOrder->id, $cusOrder->seller_id, auth()->id());
                CustomerOrderNotification::unAssignCustomerOrder($cusOrder->id, $oldSellerId, auth()->id());
            }

            DB::commit();

            return $this->respondSuccessData($cusOrder, 'Sửa thông tin đơn hàng thành công');
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function update2(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            /** @var CustomerOrder $cusOrder */
            $cusOrder = CustomerOrder::findOrFail($id);
            $this->authorize('update', $cusOrder);

            collect($request->input('customer_order_items', []))
                ->each(function ($item) {
                    $cusOrderItem = CustomerOrderItem::findOrFail($item['id']);
                    $cusOrderItem->fill($item);
                    $cusOrderItem->save();
                });

            // update delivery_type, insurance_type, reinforced_type, fee_ship_inland
            foreach ($request->input('shop_bill_codes', []) as $shopId => $billCodeAttr) {
                if (!$shopId || empty($billCodeAttr)) continue;
                /** @var BillCode $billCode */
                $billCode = BillCode::firstOrNew([
                    'customer_order_id' => $cusOrder->id,
                    'shop_id' => $shopId,
                ]);
                $billCode->fill($billCodeAttr);
                $billCode->save();
            }


            DB::commit();

//            $cusOrder->load('CustomerOrderItems', 'CustomerOrderItems.images', 'CustomerOrderItems.shop', 'customer', 'seller', 'customer.customerAddresses');
            $cusOrder->load([
                'CustomerOrderItems',
                'CustomerOrderItems.images',
                'CustomerOrderItems.shop',
                'seller',
                'customer',
                'customer.customerAddresses',
                'billCodes',
                'billCodes.ladingCodes',
            ]);

            return $this->respondSuccessData($cusOrder, 'Sửa thông tin đơn hàng thành công');
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $this->authorize('delete', CustomerOrder::class);

        DB::beginTransaction();
        try {
            $cusOrder = CustomerOrder::findOrFail($id);
            $cusOrder->delete();

            //UPDATE STATUS OF TASK.
            CustomerOrderTask::deleteCustomerOrderByUser($cusOrder->id);
            //END PROCESS

            // BEGIN NOTIFICATION
            CustomerOrderNotification::deleteCustomerOrderByManagement($cusOrder->id, $cusOrder->seller_id, auth()->id());
            //END

            DB::commit();

            return $this->respondSuccessData([], 'Xóa đơn hàng thành công');
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    public function prepareForm()
    {
        return $this->respondSuccessData([
            'rate' => 1.5,
        ]);
    }

    public function tracking(Request $request){
        $data = $request->all();
        $url = env('API_TRACKING');

        // call api
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $data);

        $output = curl_exec($ch);

        curl_close($ch);

        if($data){
            return $this->respondSuccessData(json_decode($output));
        }
    }
}
