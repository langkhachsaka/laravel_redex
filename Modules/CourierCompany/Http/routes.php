<?php

Route::group(['middleware' => 'auth:api', 'prefix' => 'api/v1', 'namespace' => 'Modules\CourierCompany\Http\Controllers'], function()
{
    Route::get('courier-company', 'CourierCompanyController@index');
    Route::post('courier-company', 'CourierCompanyController@store');
    Route::post('courier-company/{id}', 'CourierCompanyController@update');
    Route::get('courier-company/{list}', 'CourierCompanyController@list')->where('list', '(list)');
    Route::get('courier-company/{id}', 'CourierCompanyController@show');
    Route::delete('courier-company/{id}', 'CourierCompanyController@destroy');
});
