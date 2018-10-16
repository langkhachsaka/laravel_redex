<?php

Route::group(
    [
        'middleware' => 'auth:api',
        'prefix' => 'api/v1',
        'namespace' => 'Modules\Shop\Http\Controllers'
    ], function() {
    Route::get('shop', 'ShopController@index');
    Route::post('shop', 'ShopController@store');
    Route::post('shop/{id}', 'ShopController@update');
    Route::get('shop/{list}', 'ShopController@list')->where('list', '(list)');
    Route::get('shop/{id}', 'ShopController@show');
    Route::delete('shop/{id}', 'ShopController@destroy');
});
