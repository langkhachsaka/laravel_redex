<?php

Route::group(['middleware' => 'auth:api', 'prefix' => 'api/v1', 'namespace' => 'Modules\WarehouseReceivingCN\Http\Controllers'], function () {
        Route::get('warehouse-receiving-cn', 'WarehouseReceivingCNController@index');
        Route::post('warehouse-receiving-cn', 'WarehouseReceivingCNController@store');
        Route::post('warehouse-receiving-cn/import', 'WarehouseReceivingCNController@importFileExcel');
        Route::post('warehouse-receiving-cn/{id}', 'WarehouseReceivingCNController@update');
        Route::get('warehouse-receiving-cn/checkStatus/{billOfLadingCode}', 'WarehouseReceivingCNController@checkStatus');
        Route::delete('warehouse-receiving-cn/{id}', 'WarehouseReceivingCNController@destroy');
    }
);