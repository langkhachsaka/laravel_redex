<?php

Route::group(['middleware' => 'auth:api', 'prefix' => 'api/v1', 'namespace' => 'Modules\WarehouseReceivingVN\Http\Controllers'], function ()
{
    Route::get('warehouse-receiving-vn', 'WarehouseReceivingVNController@index');
    Route::get('warehouse-receiving-vn/{id}', 'WarehouseReceivingVNController@show');
    Route::get('warehouse-receiving-vn/checkShipmentCode/{id}', 'WarehouseReceivingVNController@checkShipmentCode');
    Route::post('warehouse-receiving-vn', 'WarehouseReceivingVNController@store');
    Route::post('warehouse-receiving-vn/createTaskVerify', 'WarehouseReceivingVNController@createTaskVerify');
    Route::post('warehouse-receiving-vn/storeShipment/{shipment_code}', 'WarehouseReceivingVNController@storeShipment');
    Route::post('warehouse-receiving-vn/saveTemp/{id}', 'WarehouseReceivingVNController@saveTemp');
    Route::post('warehouse-receiving-vn/submitData/{id}', 'WarehouseReceivingVNController@submitData');

/*    Route::post('warehouse-receiving-vn/approve', 'WarehouseReceivingVNController@approveShipment');
    Route::post('warehouse-receiving-vn/report', 'WarehouseReceivingVNController@reportShipment');*/
    Route::post('warehouse-receiving-vn/{id}', 'WarehouseReceivingVNController@update');
    /*Route::get('warehouse-receiving-cn/checkStatus/{billOfLadingCode}', 'WarehouseReceivingCNController@checkStatus');*/
    Route::delete('warehouse-receiving-vn/{id}', 'WarehouseReceivingVNController@destroy');
});
