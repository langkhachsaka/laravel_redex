<?php

Route::group(
    [
        'middleware' => 'auth:api',
        'prefix' => 'api/v1',
        'namespace' => 'Modules\Customer\Http\Controllers'
    ], function() {
    Route::get('customer', 'CustomerController@index');
    Route::post('customer', 'CustomerController@store');
    Route::post('customer/{id}', 'CustomerController@update');
    Route::get('customer/{list}', 'CustomerController@list')->where('list', '(list)');
    Route::get('customer/{id}', 'CustomerController@show');
    Route::delete('customer/{id}', 'CustomerController@destroy');

    Route::get('customer-address', 'CustomerAddressController@index');
    Route::post('customer-address', 'CustomerAddressController@store');
    Route::post('customer-address/{id}', 'CustomerAddressController@update');
    Route::get('customer-address/{id}', 'CustomerAddressController@show');
    Route::delete('customer-address/{id}', 'CustomerAddressController@destroy');

    Route::post('customer/{id}/recharge','CustomerController@recharge');
});
