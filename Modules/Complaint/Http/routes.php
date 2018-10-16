<?php

Route::group(['middleware' => 'auth:api', 'prefix' => 'api/v1', 'namespace' => 'Modules\Complaint\Http\Controllers'], function()
{
    Route::get('complaint', 'ComplaintController@index');
    Route::post('complaint', 'ComplaintController@store');
    Route::post('complaint/adminConfirm/{id}', 'ComplaintController@adminConfirm');
    Route::post('complaint/customerServiceConfirm/{id}', 'ComplaintController@customerServiceConfirm');
    Route::post('complaint/orderOfficerConfirm/{id}', 'ComplaintController@orderOfficerConfirm');
    Route::post('complaint/{id}', 'ComplaintController@update');
    Route::get('complaint/{id}', 'ComplaintController@show');
    Route::delete('complaint/{id}', 'ComplaintController@destroy');

    Route::get('complaint-history', 'ComplaintHistoryController@index');
    Route::post('complaint-history', 'ComplaintHistoryController@store');
    Route::post('complaint-history/{id}', 'ComplaintHistoryController@update');
    Route::get('complaint-history/{id}', 'ComplaintHistoryController@show');
    Route::delete('complaint-history/{id}', 'ComplaintHistoryController@destroy');
});
