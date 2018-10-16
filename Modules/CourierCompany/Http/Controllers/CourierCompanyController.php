<?php

namespace Modules\CourierCompany\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Base\Http\Controllers\Controller;
use Modules\CourierCompany\Models\CourierCompany;

class CourierCompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {
        $this->authorize('index', CourierCompany::class);

        return $this->respondSuccessData(CourierCompany::all());
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
        $this->authorize('create', CourierCompany::class);

        $courierCompany = new CourierCompany();

        $requestData = $request->all();
        $validator = $this->validateRequestData($requestData);
        if ($validator->fails()) {
            return $this->respondInvalidData($validator->messages());
        }

        $courierCompany->fill($requestData);
        $courierCompany->save();

        return $this->respondSuccessData($courierCompany);
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
        $this->authorize('view', CourierCompany::class);

        /** @var CourierCompany $courierCompany */
        $courierCompany = CourierCompany::findOrFail($id);

        return $this->respondSuccessData($courierCompany);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function list(Request $request)
    {
        $query = CourierCompany::query()->limit(20);

        if ($request->has('q')) {
            $query->whereFullLike('name', $request->input('q'));
        }

        return ['results' => $query->get(['id', 'name as text'])];
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
        $this->authorize('update', CourierCompany::class);

        $courierCompany = CourierCompany::findOrFail($id);

        $requestData = $request->all();
        $validator = $this->validateRequestData($requestData);

        if ($validator->fails()) {
            return $this->respondInvalidData($validator->messages());
        }

        $courierCompany->fill($requestData);
        $courierCompany->save();

        return $this->respondSuccessData(
            $courierCompany,
            'Sửa thông tin công ty chuyển phát thành công'
        );
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
        $this->authorize('delete', CourierCompany::class);

        $courier = CourierCompany::findOrFail($id);

        $courier->delete();

        return $this->respondSuccessData([], 'Xóa công ty chuyển phát thành công');
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
                'name' => 'bail|required|unique:area_codes|string|max:255',
            ],
            [
                'name.required' => 'Chưa nhập tên công ty chuyển phát',
                'name.unique' => 'Tên công ty đã tồn tại trong hệ thống',
                'name.max' => 'Tên công ty chứa tối đa 225 ký tự',
            ]
        );
    }
}
