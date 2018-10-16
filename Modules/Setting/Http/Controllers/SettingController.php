<?php

namespace Modules\Setting\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Base\Http\Controllers\Controller;
use Modules\Setting\Models\Setting;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $setting = $this->getSetting();

        return $this->respondSuccessData($setting);
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request)
    {
        //$this->authorize('update', Setting::class);

        $setting = $this->getSetting();

        $requestData = $request->input();

        $validator = $this->validateRequestData($requestData);

        if ($validator->fails()) {
            return $this->respondInvalidData($validator->messages());
        }

        $setting->fill($requestData);
        $setting->save();

        return $this->respondSuccessData($setting);
    }

    private function getSetting()
    {
        $setting = Setting::first();

        if (!$setting) {
            $setting = new Setting();
            $setting->save();
        }

        return $setting;
    }

    private function validateRequestData($requestData)
    {
        return \Validator::make(
            $requestData,
            [
                'error_weight' => 'bail|required',
                'error_size' => 'bail|required',
                'factor_conversion' => 'bail|required',
                'discount_link' => 'bail|string',
            ]
//            ,
//            [
//                'error_weight.required' => 'Chưa nhập error_weight',
//            ]
        );
    }

}
