<?php

Route::group(
    [
        'middleware' => 'api',
        'prefix' => 'api/v1',
        'namespace' => 'Modules\VerifyLadingCode\Http\Controllers'
    ], function() {
    Route::get('verify-lading-code', 'VerifyLadingCodeController@index');
    Route::post('verify-lading-code/storeVerifyBillOfLading', 'VerifyLadingCodeController@storeVerifyBillOfLading');
    Route::post('verify-lading-code/storeVerifyCustomerOrder', 'VerifyLadingCodeController@storeVerifyCustomerOrder');
    Route::post('verify-lading-code/storeVerifyManyCustomerOrder', 'VerifyLadingCodeController@storeVerifyManyCustomerOrder');
    Route::post('verify-lading-code/{id}', 'VerifyLadingCodeController@update');
    Route::delete('verify-lading-code/{id}', 'VerifyLadingCodeController@destroy');
    Route::get('verify-lading-code/{id}', 'VerifyLadingCodeController@show');
    Route::get('verify-lading-code/checkLadingCode/{id}', 'VerifyLadingCodeController@checkLadingCode');
    Route::get('verify-lading-code/getCustomerOrder/{id}', 'VerifyLadingCodeController@getCustomerOrder');
    Route::get('verify-lading-code/getCustomerOrderDetail/{id}', 'VerifyLadingCodeController@getCustomerOrderDetail');


});
