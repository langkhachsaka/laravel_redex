<?php

Route::group(['middleware' => 'auth:api', 'prefix' => 'api/v1', 'namespace' => 'Modules\Statistical\Http\Controllers'], function()
{
    Route::get('dashboard/get-sum-customer-order', 'StatisticalController@getSumCustomerOrder');

    // route api customeroder
    Route::get('statistical/get-sum-customer-order', 'CustomerOrderStatisticalController@getSumCustomerOrder');
    Route::get('statistical/get-sum-customer-order-finished', 'CustomerOrderStatisticalController@getSumCustomerOrderFinished');
    Route::get('statistical/get-list-sum-customer-order-by-user', 'CustomerOrderStatisticalController@getListSumCustomerOrderByUser');
    Route::get('statistical/get-list-sum-customer-order-one-year', 'CustomerOrderStatisticalController@getListSumCustomerOrderOneYear');
    Route::get('statistical/get-list-sum-customer-order', 'CustomerOrderStatisticalController@getListSumCustomerOrder');


    Route::get('statistical/get-list-sum-transaction', 'TransactionStatisticalController@getListSumTransaction');
});
