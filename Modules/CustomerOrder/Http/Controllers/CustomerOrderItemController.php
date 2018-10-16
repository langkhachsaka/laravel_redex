<?php

namespace Modules\CustomerOrder\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Base\Http\Controllers\Controller;
use Modules\CustomerOrder\Models\CustomerOrderItem;
use Modules\Image\Models\Image;

class CustomerOrderItemController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $perPage = $this->getPerPage($request);
        $cusOrderItems = CustomerOrderItem::paginate($perPage);
        $cusOrderItems->load('ladingCodes');

        return $this->respondSuccessData($cusOrderItems);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function validateItem(Request $request)
    {
        $cusOrderItem = new CustomerOrderItem(['status' => CustomerOrderItem::STATUS_PENDING]);
        $requestData = $request->all();
        $validator = $this->validateRequestData($requestData);

        if ($validator->fails()) {
            return $this->respondInvalidData($validator->messages());
        }

        $cusOrderItem->fill($requestData);

        $cusOrderItem->quantity = (int)$cusOrderItem->quantity;

        $cusOrderItem->id = $request->input('id', time());
        $cusOrderItem->load('shop', 'images', 'ladingCodes');

        if (is_array($request->input('images'))) {
            foreach ($request->input('images') as $img) {
                $image = new Image();
                $image->path = $img;
                $image->imagetable_id = $cusOrderItem->id;
                $image->imagetable_type = CustomerOrderItem::class;

                $cusOrderItem->images->push($image);
            }
        }

        return $this->respondSuccessData($cusOrderItem, 'Thêm sản phẩm vào danh sách thành công');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function import(Request $request)
    {
        DB::beginTransaction();
        try {
            $file = $request->file('file');
            $customerOrderItems = [];
            $results = collect([]);

            Excel::load($file, function ($reader) use (&$results) {
                $results = $reader->get();
            });

            foreach ($results as $key => $row) {
                if (!is_array($row)) {
                    $item = $row->toArray();
                } else {
                    $item = $row;
                }

                $customerOrderItem = new CustomerOrderItem();

                if ($item['stt']) {
                    $customerOrderItem->description = $item['mo_ta'];
                    $customerOrderItem->link = $item['link_sp'];
                    $customerOrderItem->size = $item['kich_co_cm'];
                    $customerOrderItem->colour = $item['mau_sac'];
                    $customerOrderItem->weight = $item['trong_luong_kg'];
                    $customerOrderItem->unit = $item['don_vi'];
                    $customerOrderItem->quantity = (int)$item['so_luong'];
                    $customerOrderItem->price_cny = $item['gia_web'];
                    $customerOrderItem->status = CustomerOrderItem::STATUS_PENDING;

                    if ($request->has('customer_order_id')) {
                        $customerOrderItem->customer_order_id = $request->input('customer_order_id');
                        $customerOrderItem->save();
                    } else {
                        $customerOrderItem->id = time().$key;
                    }

                    $customerOrderItem->load('images', 'shop', 'ladingCodes');

                    array_push($customerOrderItems, $customerOrderItem);
                } else {
                    break;
                }
            }

            DB::commit();
            return $this->respondSuccessData(
                $customerOrderItems,
                $request->has('customer_order_id')
                    ?
                    'Thêm sản phẩm từ file thành công'
                    :
                    'Đọc sản phẩm từ file thành công'
            );
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $cusOrderItem = new CustomerOrderItem(['status' => CustomerOrderItem::STATUS_PENDING]);
            $requestData = $request->all();
            $validator = $this->validateRequestData($requestData);

            if ($validator->fails()) {
                return $this->respondInvalidData($validator->messages());
            }

            $cusOrderItem->fill($requestData);
            $cusOrderItem->save();

            if (is_array($request->input('images'))) {
                foreach ($request->input('images') as $img) {
                    $image = new Image();
                    $image->path = $img;
                    $image->imagetable_id = $cusOrderItem->id;
                    $image->imagetable_type = CustomerOrderItem::class;
                    $image->save();
                }
            }

            $cusOrderItem->load('images', 'shop', 'ladingCodes');

            DB::commit();
            return $this->respondSuccessData($cusOrderItem, 'Thêm sản phẩm mới thành công');
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
        $cusOrderItem = CustomerOrderItem::with('images')
            ->with('shop')
            ->findOrFail($id);

        return $this->respondSuccessData($cusOrderItem);
    }

    public function listAvailable()
    {
        $data = CustomerOrderItem::with(
            'images',
            'shop',
            'customerOrder',
            'customerOrder.customer',
            'ladingCodes'
        )
            ->where('customer_order_items.status', CustomerOrderItem::STATUS_APPROVED)
            ->where('quantity', '>', 'quantity_in_progress')
            ->get();

        return $this->respondSuccessData($data);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            /** @var CustomerOrderItem $cusOrderItem */
            $cusOrderItem = CustomerOrderItem::findOrFail($id);
            $requestData = $request->all();
            $validator = $this->validateRequestData($requestData);
            if ($validator->fails()) {
                return $this->respondInvalidData($validator->messages());
            }

            $cusOrderItem->fill($requestData);
            $cusOrderItem->save();

            if (is_array($request->input('images'))) {
                Image::where('imagetable_id', $id)
                    ->where('imagetable_type', '=', CustomerOrderItem::class)
                    ->delete();

                for ($i = 0; $i < count($request['images']); $i++) {
                    $image = new Image();
                    $image->path = $request['images'][$i];
                    $image->imagetable_id = $cusOrderItem->id;
                    $image->imagetable_type = CustomerOrderItem::class;
                    $image->save();
                }
            }

            $cusOrderItem->load('images', 'shop', 'ladingCodes');

            DB::commit();
            return $this->respondSuccessData($cusOrderItem);
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    public function updateBillCode(Request $request, $id)
    {
        /** @var CustomerOrderItem $cusOrderItem */
        $cusOrderItem = CustomerOrderItem::findOrFail($id);
        $requestData = $request->input();

        $cusOrderItem->fill($requestData);

        $cusOrderItem->save();

        $cusOrderItem->load('images', 'shop', 'ladingCodes');

        return $this->respondSuccessData($cusOrderItem);
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
            $cusOrderItem = CustomerOrderItem::findOrFail($id);
            $this->destroyImages($id, CustomerOrderItem::class);
            $cusOrderItem->delete();

            DB::commit();
            return $this->respondSuccessData([], 'Xóa sản phẩm thành công');
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    public function alertToCustomerNotEnough($id, $shop_quantity){
        $customerOrderItem = CustomerOrderItem::findOrFail($id);
        $customerOrderItem->shop_quantity = $shop_quantity;
        $customerOrderItem->alerted = 1;
        $customerOrderItem->save();
        return $this->respondSuccessData([], 'Cảnh báo thành công');
    }
    /**
     * @param $requestData
     * @return \Illuminate\Validation\Validator
     */
    public function validateRequestData($requestData)
    {
        return \Validator::make(
            $requestData,
            [
                'link' => 'bail|required|string',
                'size' => 'bail|required|string|max:255',
                'colour' => 'bail|required|string|max:255',
                'unit' => 'bail|required|string|max:255',
                'quantity' => 'bail|required|numeric|max:4294967295|min:1',
                'price_cny' => 'bail|required|numeric|max:9999999999999|min:0',
                'weight' => 'bail|nullable|numeric|max:999999|min:0',
                'volume' => 'bail|nullable|numeric|max:999999|min:0',
                'discount' => 'bail|nullable|string|max:255',
                'fee_inland_ship' => 'bail|nullable|numeric|max:99999999|min:0',

            ],
            [
                'link.required' => 'Chưa nhập link sản phẩm',
                'size.required' => 'Chưa nhập size sản phẩm',
                'size.max' => 'Size chứa tối đa 225 ký tự',
                'colour.required' => 'Chưa nhập màu sắc sản phẩm',
                'colour.max' => 'Màu sắc chứa tối đa 225 ký tự',
                'unit.required' => 'Chưa nhập đơn vị sản phẩm',
                'unit.max' => 'Đơn vị chứa tối đa 225 ký tự',
                'quantity.numeric' => 'Số lượng sản phẩm phải là số',
                'quantity.max' => 'Số lượng phải nhỏ hơn 4.294.967.295',
                'quantity.min' => 'Số lượng phải lớn hơn hoặc bằng 1',
                'price_cny.numeric' => 'Giá sản phẩm phải là số',
                'price_cny.max' => 'Giá sản phẩm phải nhỏ hơn 9.999.999.999.999',
                'price_cny.min' => 'Giá sản phẩm phải lớn hơn hoặc bằng 0',
                'weight.max' => 'Giá sản phẩm phải nhỏ hơn 999.999',
                'weight.min' => 'Giá sản phẩm phải lớn hơn hoặc bằng 0',
                'volume.max' => 'Giá sản phẩm phải nhỏ hơn 999.999',
                'volume.min' => 'Giá sản phẩm phải lớn hơn hoặc bằng 0',
                'discount.max' => 'Độ dài tối đa cho phép là 255',
                'fee_inland_ship.max' => 'Giá sản phẩm phải nhỏ hơn 99.999.999',
                'fee_inland_ship.min' => 'Giá sản phẩm phải lớn hơn hoặc bằng 0',
            ]
        );
    }
}
