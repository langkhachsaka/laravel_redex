<?php

namespace Modules\Inventory\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Base\Http\Controllers\Controller;
use Modules\Inventory\Models\Inventory;

class InventoryController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorize('index', Inventory::class);

        $perPage = $this->getPerPage($request);

        $inventories = Inventory::with('shop')
            ->whereFullLike('bill_of_lading_code', $request->input('bill_of_lading_code'))
            ->whereFullLike('invoice_code', $request->input('invoice_code'))
            ->whereFullLike('description', $request->input('description'))
            ->filterWhere('date_receiving', '>=', $request->input('date_receiving_from'))
            ->filterWhere('date_receiving', '<=', $request->input('date_receiving_to'))
            ->paginate($perPage);

        return $this->respondSuccessData($inventories);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request)
    {
        $this->authorize('create', Inventory::class);

        $inventory = new Inventory();

        $validator = $this->validateRequestData($request->input());

        if ($validator->fails()) {
            return $this->respondInvalidData($validator->messages());
        }

        $inventory->fill($request->all());
        $inventory->save();

        $inventory->load('shop');
        return $this->respondSuccessData($inventory, 'Thêm thành công');
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show($id)
    {
        $this->authorize('view', Inventory::class);

        /** @var Inventory $inventory */
        $inventory = Inventory::findOrFail($id);

        $inventory->load('shop');

        return $this->respondSuccessData($inventory);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, $id)
    {
        $this->authorize('update', Inventory::class);

        $validator = $this->validateRequestData($request->input());

        if ($validator->fails()) {
            return $this->respondInvalidData($validator->messages());
        }

        /** @var Inventory $inventory */
        $inventory = Inventory::findOrFail($id);

        $inventory->fill($request->all());
        $inventory->save();

        $inventory->load('shop');
        return $this->respondSuccessData($inventory);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $this->authorize('delete', Inventory::class);

        /** @var Inventory $inventory */
        $inventory = Inventory::findOrFail($id);

        $inventory->delete();

        return $this->respondSuccessData([], 'Xóa thành công');
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
                'invoice_code' => 'bail|required|string|max:255',
                'bill_of_lading_code' => 'bail|required|string|max:255',
                'reason' => 'bail|required|string',
                'description' => 'bail|required|string',
            ],
            [
                'invoice_code.required' => 'Chưa nhập mã hóa đơn',
                'invoice_code.max' => 'Mã hóa đơn tối đa 225 ký tự',
                'bill_of_lading_code.required' => 'Chưa nhập mã vận đơn',
                'bill_of_lading_code.max' => 'Mã vận đơn đa 225 ký tự',
                'reason.required' => 'Chưa nhập lý do nhập hàng tồn kho',
                'description.required' => 'Chưa nhập mô tả hàng',
            ]
        );
    }
}
