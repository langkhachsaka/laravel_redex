<?php

Route::group(['middleware' => 'auth:api', 'prefix' => 'api/v1', 'namespace' => 'Modules\Rate\Http\Controllers'], function()
{
    Route::get('rate', 'RateController@index');
    Route::post('rate', 'RateController@store');
    Route::post('rate/{id}', 'RateController@update');
    Route::get('rate/{id}', 'RateController@show');
    Route::delete('rate/{id}', 'RateController@destroy');
});
