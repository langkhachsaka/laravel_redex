<?php

Route::group(['middleware' => 'auth:api', 'prefix' => 'api/v1/setting', 'namespace' => 'Modules\Setting\Http\Controllers'], function()
{
    Route::get('/', 'SettingController@index');
    Route::post('/', 'SettingController@update');
});
