<?php

Route::group(['middleware' => 'auth:api', 'prefix' => 'api/v1', 'namespace' => 'Modules\Warehouse\Http\Controllers'],
    function () {
        Route::get('warehouse', 'WarehouseController@index');
        Route::post('warehouse', 'WarehouseController@store');
        Route::get('warehouse/{list}', 'WarehouseController@list')->where('list', '(list)');
        Route::post('warehouse/{id}', 'WarehouseController@update');
        Route::delete('warehouse/{id}', 'WarehouseController@destroy');
    }
);
