<?php

Route::group(['middleware' => 'auth:api', 'prefix' => 'api/v1', 'namespace' => 'Modules\BillOfLading\Http\Controllers'], function()
{
    Route::get('bill-of-lading', 'BillOfLadingController@index');
    Route::post('bill-of-lading', 'BillOfLadingController@store');
    Route::post('bill-of-lading/{id}/approve', 'BillOfLadingController@approve');
    Route::post('bill-of-lading/{id}', 'BillOfLadingController@update');
    Route::get('bill-of-lading/{id}', 'BillOfLadingController@show');
    Route::delete('bill-of-lading/{id}', 'BillOfLadingController@destroy');
});
