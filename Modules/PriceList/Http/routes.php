<?php

Route::group(['middleware' => 'auth:api', 'prefix' => 'api/v1', 'namespace' => 'Modules\PriceList\Http\Controllers'], function()
{
    Route::get('/price-list', 'PriceListController@index');
    Route::post('/price-list','PriceListController@update');
});
