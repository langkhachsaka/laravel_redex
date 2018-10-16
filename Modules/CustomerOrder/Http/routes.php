<?php

Route::group(['middleware' => 'auth:api', 'prefix' => 'api/v1', 'namespace' => 'Modules\CustomerOrder\Http\Controllers'], function()
{
    Route::get('customer-order/prepare-form', 'CustomerOrderController@prepareForm');
    Route::get('customer-order/tracking','CustomerOrderController@tracking');
    Route::get('customer-order', 'CustomerOrderController@index');
    Route::post('customer-order', 'CustomerOrderController@store');
    Route::post('customer-order/{id}', 'CustomerOrderController@update');
    Route::post('customer-order/{id}/update2', 'CustomerOrderController@update2');
    Route::post('customer-order/{id}/approve', 'CustomerOrderController@approve');
//    Route::post('customer-order/{customerOrderId}/import', 'CustomerOrderController@import');
    Route::get('customer-order/{id}', 'CustomerOrderController@show');
    Route::delete('customer-order/{id}', 'CustomerOrderController@destroy');

    Route::get('customer-order-item', 'CustomerOrderItemController@index');
    Route::post('customer-order-item/validate', 'CustomerOrderItemController@validateItem');
    Route::post('customer-order-item/import', 'CustomerOrderItemController@import');
    Route::post('customer-order-item', 'CustomerOrderItemController@store');
    Route::post('customer-order-item/{id}', 'CustomerOrderItemController@update');
    Route::post('customer-order-item/update-bill-code/{id}', 'CustomerOrderItemController@updateBillCode');
    Route::post('customer-order-item/alert-to-customer-not-enough/{id}/{shop_quantity}', 'CustomerOrderItemController@alertToCustomerNotEnough');
    Route::get('customer-order-item/{list}', 'CustomerOrderItemController@listAvailable')
        ->where('list', '(list-available)');
    Route::get('customer-order-item/{id}', 'CustomerOrderItemController@show');
    Route::delete('customer-order-item/{id}', 'CustomerOrderItemController@destroy');

});
