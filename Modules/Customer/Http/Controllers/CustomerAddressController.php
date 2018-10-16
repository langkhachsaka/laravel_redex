<?php

namespace Modules\Customer\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Base\Http\Controllers\Controller;
use Modules\Customer\Models\CustomerAddress;

class CustomerAddressController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $cusAddress = new CustomerAddress();

        $requestData = $request->all();
        $validator = $this->validateRequestData($requestData);

        if ($validator->fails()) {
            return $this->respondInvalidData($validator->messages());
        }

        $cusAddress->fill($requestData);
        $cusAddress->save();

        return $this->respondSuccessData($cusAddress, 'Thêm địa chỉ thành công');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $cusAddress = CustomerAddress::findOrFail($id);

        return $this->respondSuccessData($cusAddress);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $cusAddress = CustomerAddress::findOrFail($id);

        $requestData = $request->all();
        $validator = $this->validateRequestData($requestData);

        if ($validator->fails()) {
            return $this->respondInvalidData($validator->messages());
        }

        $cusAddress->fill($requestData);
        $cusAddress->save();

        return $this->respondSuccessData($cusAddress, 'Sửa địa chỉ thành công');
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
                'phone' => 'bail|required|string|max:255',
                'name' => 'bail|required|string|max:255',
                'address' => 'bail|required||string',
            ],
            [
                'name.required' => 'Chưa nhập tên',
                'name.max' => 'Tên chứa tối đa 225 ký tự',
                'phone.required' => 'Chưa nhập số điện thoại',
                'phone.max' => 'Số điện thoại tối đa 255 ký tự',
                'address.required' => 'Chưa nhập số địa chỉ cụ thể',
            ]
        );
    }
}
