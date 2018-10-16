<?php

Route::group(['middleware' => 'auth:api', 'prefix' => 'api/v1', 'namespace' => 'Modules\Delivery\Http\Controllers'], function()
{
    Route::get('delivery', 'DeliveryController@index');
    Route::post('delivery', 'DeliveryController@store');
    Route::post('delivery/createTaskDelivery', 'DeliveryController@createTaskDelivery');
//    Route::post('delivery/{id}', 'DeliveryController@update');
    Route::get('delivery/{id}', 'DeliveryController@show');
    Route::delete('delivery/{id}', 'DeliveryController@destroy');
    Route::post('/delivery/confirm/{id}', 'DeliveryController@confirm');
});

