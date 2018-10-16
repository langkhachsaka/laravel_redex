<?php

Route::group(['middleware' => 'auth:api', 'prefix' => 'api/v1', 'namespace' => 'Modules\Inventory\Http\Controllers'], function()
{
    Route::get('inventory', 'InventoryController@index');
    Route::post('inventory', 'InventoryController@store');
    Route::post('inventory/{id}', 'InventoryController@update');
    Route::get('inventory/{id}', 'InventoryController@show');
    Route::delete('inventory/{id}', 'InventoryController@destroy');
});
