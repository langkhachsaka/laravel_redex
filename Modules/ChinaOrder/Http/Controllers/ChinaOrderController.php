<?php

namespace Modules\ChinaOrder\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Base\Http\Controllers\Controller;
use Modules\ChinaOrder\Models\ChinaOrder;
use Modules\ChinaOrder\Models\ChinaOrderItem;
use Modules\Notification\Models\ChinaOrderNotification;
use Modules\User\Models\User;

class ChinaOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorize('index', ChinaOrder::class);

        $perPage = $this->getPerPage($request);

        /** Ordering Officer can view only their orders
         */
        $user_purchasing_id = auth()->user()->isOrderingOfficer()
            ? auth()->id()
            : $request->input('user_purchasing_id');

        /** Accountant can view only approved orders */
        $china_order_status = auth()->user()->isAccountant()
            ? ChinaOrder::STATUS_APPROVED
            : $request->input('status');

        $chinaOrders = ChinaOrder::with(
            'chinaOrderItems',
            'chinaOrderItems.images',
            'chinaOrderItems.customerOrderItem',
            'chinaOrderItems.customerOrderItem.images',
            'chinaOrderItems.customerOrderItem.shop',
            'userPurchasing'
        )
            ->filterWhere('china_orders.user_purchasing_id', '=', $user_purchasing_id)
            ->filterWhere('china_orders.created_at', '>=', $request->input('created_at_from'))
            ->filterWhere('china_orders.created_at', '<=', $request->input('created_at_to'))
            ->filterWhere('china_orders.status', '=', $china_order_status)
            ->orderby('china_orders.id', 'desc')
            ->paginate($perPage);

        return $this->respondSuccessData($chinaOrders);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function store(Request $request)
    {
        $this->authorize('create', ChinaOrder::class);

        DB::beginTransaction();
        try {
            $chinaOrder = new ChinaOrder();

            $chinaOrder->fill($request->all());
            $chinaOrder->status = ChinaOrder::STATUS_PENDING;
            $chinaOrder->save();

            collect($request->input('items', []))
                ->each(function ($item) use ($chinaOrder) {
                    $cnOrderItem = new ChinaOrderItem();

                    $cnOrderItem->fill($item);

                    $cnOrderItem->status = ChinaOrderItem::STATUS_PENDING;
                    $cnOrderItem->chinaOrder()->associate($chinaOrder);
                    $cnOrderItem->save();

                    $cnOrderItem->customerOrderItem->quantity_in_progress += $cnOrderItem->quantity;
                    $cnOrderItem->customerOrderItem->save();
                });

            // BEGIN NOTIFICATION
            ChinaOrderNotification::newChinaOrder($chinaOrder->id, auth()->id());
            ChinaOrderNotification::assignChinaOrder($chinaOrder->id, $chinaOrder->user_purchasing_id, auth()->id());
            // END

            DB::commit();
            return $this->respondSuccessData($chinaOrder, 'Tạo đơn hàng thành công');
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
        $chinaOrder = ChinaOrder::with(
            'chinaOrderItems',
            'chinaOrderItems.images',
            'chinaOrderItems.customerOrderItem',
            'chinaOrderItems.customerOrderItem.images',
            'chinaOrderItems.customerOrderItem.shop',
            'userPurchasing'
        )->findOrFail($id);

        $this->authorize('view', $chinaOrder);

        return $this->respondSuccessData($chinaOrder);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function approve($id)
    {
        $this->authorize('approve', ChinaOrder::class);

        DB::beginTransaction();
        try {
            $cnOrder = ChinaOrder::findOrFail($id);
            $cnOrder->status = ChinaOrder::STATUS_APPROVED;
            $cnOrder->save();

            ChinaOrderItem::where('china_order_id', $id)
                ->update(['china_order_items.status' => ChinaOrderItem::STATUS_APPROVED]);

            // BEGIN NOTIFICATION
            ChinaOrderNotification::approveChinaOrder($cnOrder->id, $cnOrder->user_purchasing_id, auth()->id());
            //END

            $cnOrder->load('chinaOrderItems',
                'chinaOrderItems.images',
                'chinaOrderItems.customerOrderItem',
                'chinaOrderItems.customerOrderItem.images',
                'chinaOrderItems.customerOrderItem.shop',
                'userPurchasing');

            DB::commit();
            return $this->respondSuccessData($cnOrder, 'Cập nhật trạng thái đơn hàng thành công');
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
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, $id)
    {
        $this->authorize('update');

        /** @var ChinaOrder $chinaOrder */
        $chinaOrder = ChinaOrder::findOrFail($id);

        // Use it when create notification
        $oldUserPurchasingID = $chinaOrder->user_purchasing_id;

        $chinaOrder->fill($request->all());
        $chinaOrder->save();

        // BEGIN NOTIFICATION
        if ($oldUserPurchasingID != $chinaOrder->user_purchasing_id) {
            ChinaOrderNotification::assignChinaOrder($chinaOrder->id, $chinaOrder->user_purchasing_id, auth()->id());
            ChinaOrderNotification::unAssignChinaOrder($chinaOrder->id, $oldUserPurchasingID, auth()->id());
        }
        // END

        $chinaOrder->load('chinaOrderItems',
            'chinaOrderItems.images',
            'chinaOrderItems.customerOrderItem',
            'chinaOrderItems.customerOrderItem.images',
            'chinaOrderItems.customerOrderItem.shop',
            'userPurchasing');

        return $this->respondSuccessData($chinaOrder, 'Sửa đơn hàng thành công');
    }


    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $this->authorize('delete', ChinaOrder::class);

        DB::beginTransaction();
        try {
            /** @var ChinaOrder $chinaOrder */
            $chinaOrder = ChinaOrder::findOrFail($id);

            foreach ($chinaOrder->chinaOrderItems as $item) {
                $this->destroyImages($item->id, 'ChinaOrderItem');
                $item->customerOrderItem->quantity_in_progress -= $item->quantity;

                $item->customerOrderItem->save();
                $item->delete();
            }

            $chinaOrder->delete();

            // BEGIN NOTIFICATION
            ChinaOrderNotification::deleteChinaOrder($chinaOrder->id, $chinaOrder->user_purchasing_id, auth()->id());
            // END

            DB::commit();
            return $this->respondSuccessData([], 'Xóa đơn hàng thành công');
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }
}
