<?php

Route::group(['middleware' => 'auth:api', 'prefix' => 'api/v1', 'namespace' => 'Modules\Transaction\Http\Controllers'], function()
{
    Route::get('transaction', 'TransactionController@index');
    Route::get('transaction/{id}', 'TransactionController@show');
    Route::get('transaction/payment-detail/{id}', 'TransactionController@paymentDetail');
    Route::get('transaction/recharge/{id}', 'TransactionController@rechargeDetail');
    Route::post('transaction', 'TransactionController@store');
    Route::post('transaction/payment-detail/{id}','TransactionController@updateShippingFee');
    Route::get('transaction/confirm/{id}','TransactionController@paymentConfirm');
    Route::get('transaction/deposit/{id}','TransactionController@depositConfirm');
//    Route::post('transaction/{id}', 'TransactionController@update');
//    Route::delete('transaction/{id}', 'TransactionController@destroy');
});
