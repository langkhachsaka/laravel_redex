<?php

namespace Modules\ChinaOrder\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Base\Http\Controllers\Controller;
use Modules\ChinaOrder\Models\ChinaOrderItem;

class ChinaOrderItemController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $perPage = $this->getPerPage($request);
        $chinaOrderItems = ChinaOrderItem::with(
            'customerOrderItem',
            'customerOrderItem.shop',
            'images'
        )
            ->orderBy('china_order_items.id', 'desc')
            ->paginate($perPage);

        return $this->respondSuccessData($chinaOrderItems);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    /*public function store(Request $request)
    {
        $cnOrderItem = new ChinaOrderItem();

        $requestData = $request->all();
        $validator = $this->validateRequestData($requestData);

        if ($validator->fails()) {
            return $this->respondInvalidData($validator->messages());
        }

        DB::beginTransaction();
        try {
            $cnOrderItem->fill($requestData);
            $cnOrderItem->save();

            $cnOrderItem->customerOrderItem->quantity_in_progress += $cnOrderItem->quantity;
            $cnOrderItem->customerOrderItem->save();

            DB::commit();
            return $this->respondSuccessData($cnOrderItem, 'Thêm sản phẩm thành công');
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }*/

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        $chinaOrderItems = [];
        try {
            collect($request->input('items', []))
                ->each(function ($item) use (&$chinaOrderItems) {
                    $validator = $this->validateRequestData($item);

                    if ($validator->fails()) {
                        return $this->respondInvalidData($validator->messages());
                    }

                    $cnOrderItem = ChinaOrderItem::firstOrNew(
                        ['china_order_id' => $item['china_order_id'],
                            'customer_order_item_id' => $item['customer_order_item_id']
                        ],
                        ['quantity' => 0, 'price_cny' => $item['china_order_id'],
                            'status' => ChinaOrderItem::STATUS_PENDING
                        ]
                    );

                    $cnOrderItem->quantity += $item['quantity'];
//                    $cnOrderItem = new ChinaOrderItem();
//                    $cnOrderItem->status = ChinaOrderItem::STATUS_PENDING;
//                    $cnOrderItem->fill($item);
                    $cnOrderItem->save();

                    $cnOrderItem->customerOrderItem->quantity_in_progress += $item['quantity'];
                    $cnOrderItem->customerOrderItem->save();

                    $cnOrderItem->load(['customerOrderItem.images', 'customerOrderItem.shop']);

                    array_push($chinaOrderItems, $cnOrderItem);
                });

            DB::commit();
            return $this->respondSuccessData($chinaOrderItems, 'Thêm sản phẩm thành công');
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
     */
    public function show($id)
    {
        $cusOrderItem = ChinaOrderItem::with(
            'customerOrderItem',
            'customerOrderItem.shop',
            'images'
        )
            ->findOrFail($id);

        return $this->respondSuccessData($cusOrderItem);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function update(Request $request, $id)
    {
        $cnOrderItem = ChinaOrderItem::findOrFail($id);

        $requestData = $request->all();
        $validator = $this->validateRequestData($requestData);

        if ($validator->fails()) {
            return $this->respondInvalidData($validator->messages());
        }

        //get gia tri quantity cu
        $oldQuantity = $cnOrderItem->quantity;

        $cnOrderItem->fill($requestData);

        $newQuantity = $cnOrderItem->quantity;

        $cnOrderItem->customerOrderItem->quantity_in_progress -= ($oldQuantity - $newQuantity);

        DB::beginTransaction();
        try {
            $cnOrderItem->save();
            $cnOrderItem->customerOrderItem->save();

            DB::commit();
            return $this->respondSuccessData(
                ChinaOrderItem::with(
                    'customerOrderItem',
                    'customerOrderItem.shop',
                    'customerOrderItem.images'
                )
                    ->findOrFail($cnOrderItem->id),
                'Sửa sản phẩm thành công'
            );
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $cnOrderItem = ChinaOrderItem::findOrFail($id);

            $this->destroyImages($id, 'ChinaOrderItem');
            $cnOrderItem->customerOrderItem->quantity_in_progress -= $cnOrderItem->quantity;

            $cnOrderItem->customerOrderItem->save();
            $cnOrderItem->delete();

            DB::commit();
            return $this->respondSuccessData([], 'Xóa sản phẩm thành công');
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
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
                'customer_order_item_id' => 'required',
            ],
            [
                'customer_order_item_id.required' => 'Chưa chọn sản phẩm',
            ]
        );
    }
}
