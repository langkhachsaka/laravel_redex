<?php

Route::group(['middleware' => 'auth:api', 'prefix' => 'api/v1', 'namespace' => 'Modules\User\Http\Controllers'], function()
{
    Route::get('user', 'UserController@index');
    Route::post('user', 'UserController@store');
    Route::post('user/{id}', 'UserController@update');
    Route::get('user/{list}', 'UserController@list')->where('list', '(list)');
    Route::get('user/{id}', 'UserController@show');
    Route::delete('user/{id}', 'UserController@destroy');
});
