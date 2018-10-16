<?php

namespace Modules\AreaCode\Http\Controllers;

use Illuminate\Http\Request;
use Modules\AreaCode\Models\AreaCode;
use Modules\Base\Http\Controllers\Controller;

class AreaCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorize('index', AreaCode::class);

        $perPage = $this->getPerPage($request);

        $arCodes = AreaCode::whereFullLike('name', $request->input('name'))
            ->whereFullLike('code', $request->input('code'))
            ->orderBy('name')
            ->paginate($perPage);

        return $this->respondSuccessData($arCodes);
    }

    /**
     * @param Request $request
     * @return array
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function list(Request $request)
    {
        $this->authorize('list', AreaCode::class);

        $query = AreaCode::query()->limit(20);
        if ($request->has('q')) {
            $query->whereFullLike('name', $request->input('q'));
        }
        return ['results' => $query->get(['id', 'name as text'])];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request)
    {
        $this->authorize('create', AreaCode::class);

        $arCode = new AreaCode();

        $requestData = $request->all();
        $validator = $this->validateRequestData($requestData);

        if ($validator->fails()) {
            return $this->respondInvalidData($validator->messages());
        }

        $arCode->fill($requestData);
        $arCode->save();

        return $this->respondSuccessData($arCode, 'Thêm mã vùng thành công');
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
        $this->authorize('view', AreaCode::class);

        $arCode = AreaCode::findOrFail($id);

        return $this->respondSuccessData($arCode);
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
        $this->authorize('update', AreaCode::class);

        $arCode = AreaCode::findOrFail($id);

        $requestData = $request->all();
        $validator = $this->validateRequestData($request, $id);

        if ($validator->fails()) {
            return $this->respondInvalidData($validator->messages());
        }

        $arCode->fill($requestData);
        $arCode->save();

        return $this->respondSuccessData($arCode, 'Thay đổi thông tin vùng thành công');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $this->authorize('delete', AreaCode::class);

        $arCode = AreaCode::findOrFail($id);

        $arCode->delete();

        return $this->respondSuccessData([], 'Xóa thành công');
    }

    /**
     * @param $requestData
     * @param int $modelID
     * @return \Illuminate\Validation\Validator
     */
    private function validateRequestData($requestData, $modelID = 0)
    {
        return \Validator::make(
            $requestData,
            [
                'code' => 'bail|required|string|max:255|unique:area_codes,code,' . $modelID,
                'name' => 'bail|required|string|max:255|unique:area_codes,name,' . $modelID,
                'delivery_fee_unit' => 'bail|numeric|max:4294967295|min:0',
            ],
            [
                'name.required' => 'Chưa nhập tên vùng',
                'name.unique' => 'Tên vùng đã tồn tại trong hệ thống',
                'name.max' => 'Tên vùng chứa tối đa 225 ký tự',
                'code.required' => 'Chưa nhập mã vùng',
                'code.max' => 'Mã vùng chứa tối đa 225 ký tự',
                'code.unique' => 'Mã vùng đã tồn tại trong hệ thống',
                'delivery_fee_unit.numeric' => 'Đơn giá phải là số',
                'delivery_fee_unit.max' => 'Đơn giá phải nhỏ hơn 4.294.967.295',
                'delivery_fee_unit.min' => 'Đơn giá phải lớn hơn hoặc bằng 0'
            ]
        );
    }
}
