<?php

Route::group(['middleware' => 'api', 'prefix' => 'api/v1', 'namespace' => 'Modules\LadingCode\Http\Controllers'], function()
{
    Route::get('lading-code', 'LadingCodeController@index');
    Route::post('lading-code', 'LadingCodeController@store');
    Route::post('lading-code/import', 'LadingCodeController@import');
    Route::post('lading-code/{bill_code}', 'LadingCodeController@update');
    Route::post('lading-code/store-lading-code-for-bill-of-lading/{id}', 'LadingCodeController@storeLadingCodeForBillOfLading');
    Route::post('lading-code/update-lading-code-for-bill-of-lading/{id}', 'LadingCodeController@updateLadingCodeForBillOfLading');
    Route::get('lading-code/get-factor-conversion-bill-of-lading', 'LadingCodeController@getFactorConversion');
    Route::get('lading-code/{id}', 'LadingCodeController@show');
    Route::delete('lading-code/{id}', 'LadingCodeController@destroy');


    Route::get('lading-code/get-order/{code}', 'LadingCodeController@getOrder');
    Route::get('lading-code/get-rate-customer-order/{id}', 'LadingCodeController@getRateCustomerOrder');
    Route::get('lading-code/get-rate-bill-of-lading/{id}', 'LadingCodeController@getRateBillOfLading');
    Route::get('lading-code/get-rate/{id}', 'LadingCodeController@getRate');
    Route::get('lading-code/get-conversion-volume/{id}', 'LadingCodeController@getConversionVolume');

    Route::get('lading-code/list-for-delivery', 'LadingCodeController@listForDelivery');
    Route::get('lading-code/get-order-items/{code}', 'LadingCodeController@getItemsByLadingCode');
});
