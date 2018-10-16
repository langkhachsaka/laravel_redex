<?php

namespace Modules\Warehouse\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Base\Http\Controllers\Controller;
use Modules\Warehouse\Models\Warehouse;

class WarehouseController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorize('index', Warehouse::class);

        $perPage = $this->getPerPage($request);

        $warehouses = Warehouse::whereFullLike('name', $request->input('name'))
            ->whereFullLike('address', $request->input('address'))
            ->filterWhere('type', '=', $request->input('type'))
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        return $this->respondSuccessData($warehouses);
    }

    /**
     * @param Request $request
     * @return array
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function list(Request $request)
    {
        $this->authorize('list', Warehouse::class);

        $query = Warehouse::query()->limit(20);
        $query->whereFullLike('name', $request->input('q'))
            ->filterWhere('type', '=', $request->input('type'));

        return ['results' => $query->get(['id', 'name as text'])];
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request)
    {
        $this->authorize('create', Warehouse::class);

        $requestData = $request->input();
        $validator = $this->validateRequestData($requestData);

        if ($validator->fails()) {
            return $this->respondInvalidData($validator->messages());
        }

        $warehouse = new Warehouse();
        $warehouse->fill($requestData);
        $warehouse->save();

        return $this->respondSuccessData($warehouse);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, $id)
    {
        $this->authorize('update', Warehouse::class);

        $warehouse = Warehouse::findOrFail($id);

        $requestData = $request->input();
        $validator = $this->validateRequestData($requestData, $id);

        if ($validator->fails()) {
            return $this->respondInvalidData($validator->messages());
        }

        $warehouse->fill($requestData);
        $warehouse->save();

        return $this->respondSuccessData($warehouse);
    }

    /**
     * @param $id
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $this->authorize('delete', Warehouse::class);

        $warehouse = Warehouse::findOrFail($id);
        $warehouse->delete();

        return $this->respondSuccessData([], 'Xóa kho hàng thành công');
    }

    /**
     * @param $requestData
     * @param int $modelId
     * @return \Illuminate\Validation\Validator
     */
    private function validateRequestData($requestData, $modelId = 0)
    {
        return \Validator::make(
            $requestData,
            [
                'name' => 'bail|required|string|max:255|unique:warehouses,name,' . $modelId,
                'address' => 'bail|required|string|max:255',
                'type' => 'required'
            ],
            [
                'name.required' => 'Chưa nhập tên kho',
                'name.unique' => 'Tên kho đã tồn tại trong hệ thống',
                'name.max' => 'Tên kho chứa tối đa 225 ký tự',
                'address.required' => 'Chưa nhập địa chỉ kho',
                'address.max' => 'Địa chỉ kho chứa tối đa 225 ký tự',
                'type.required' => 'Chưa chọn loại kho'
            ]
        );
    }
}
