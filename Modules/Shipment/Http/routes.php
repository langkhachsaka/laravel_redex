<?php

Route::group(['middleware' => 'auth:api', 'prefix' => 'api/v1', 'namespace' => 'Modules\Shipment\Http\Controllers'], function () {
    Route::get('shipment', 'ShipmentController@index');
    Route::post('shipment', 'ShipmentController@store');
    Route::post('shipment/create', 'ShipmentController@create');
    Route::post('shipment/storeShipmentItem', 'ShipmentController@storeShipmentItem');
    Route::post('shipment/createTaskReceiveShipment', 'ShipmentController@createTaskReceiveShipment');
    Route::get('shipment/listBillOfLading', 'ShipmentController@listBillOfLading');
    Route::post('shipment/{id}', 'ShipmentController@update');
    Route::get('shipment/list', 'ShipmentController@list');
    Route::get('shipment/getShipmentInfo', 'ShipmentController@getShipmentInfo');
    Route::get('shipment/getBillOfLadingInfo/{id}', 'ShipmentController@getBillOfLadingInfo');
    Route::get('shipment/getNewShipmentCode', 'ShipmentController@getNewShipmentCode');
    Route::delete('shipment/{id}', 'ShipmentController@destroy');
    Route::delete('shipment/deleteShipmentItem/{id}', 'ShipmentController@deleteShipmentItem');
}
);