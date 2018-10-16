<?php

Route::group(['middleware' => 'auth:api', 'prefix' => 'api/v1', 'namespace' => 'Modules\Blog\Http\Controllers'], function()
{
    Route::get('blog', 'BlogController@index');
    Route::post('blog', 'BlogController@store');
    Route::get('blog/{list}', 'BlogController@list')->where('list','(list)');
    Route::post('blog/{id}', 'BlogController@update');
    Route::delete('blog/{id}', 'BlogController@destroy');
});
