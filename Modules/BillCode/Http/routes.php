<?php

Route::group(['middleware' => 'api', 'prefix' => 'api/v1', 'namespace' => 'Modules\BillCode\Http\Controllers'], function()
{
    Route::get('bill-code/{id}', 'BillCodeController@show');
    Route::post('bill-code', 'BillCodeController@store');
    Route::post('bill-code/{id}', 'BillCodeController@update');
});
