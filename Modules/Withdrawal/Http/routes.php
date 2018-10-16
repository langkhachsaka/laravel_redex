<?php

Route::group(['middleware'=>'web','prefix' => 'customer', 'namespace' => 'Modules\Withdrawal\Http\Controllers'], function()
{
    Route::get('/', 'WithdrawalController@index');
});
