<?php

Route::group(['middleware' => 'auth:api', 'prefix' => 'api/v1', 'namespace' => 'Modules\ChinaOrder\Http\Controllers'], function()
{
    Route::get('china-order', 'ChinaOrderController@index');
    Route::post('china-order', 'ChinaOrderController@store');
    Route::post('china-order/{id}/approve', 'ChinaOrderController@approve');
    Route::post('china-order/{id}', 'ChinaOrderController@update');
    Route::get('china-order/{id}', 'ChinaOrderController@show');
    Route::delete('china-order/{id}', 'ChinaOrderController@destroy');

    Route::get('china-order-item', 'ChinaOrderItemController@index');
    Route::post('china-order-item', 'ChinaOrderItemController@store');
    Route::post('china-order-item/{id}', 'ChinaOrderItemController@update');
    Route::get('china-order-item/{id}', 'ChinaOrderItemController@show');
    Route::delete('china-order-item/{id}', 'ChinaOrderItemController@destroy');
});
