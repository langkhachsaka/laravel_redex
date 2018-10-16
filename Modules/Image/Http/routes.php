<?php

Route::group(['middleware' => 'api', 'prefix' => 'api/v1', 'namespace' => 'Modules\Image\Http\Controllers'], function()
{
    Route::get('image', 'ImageController@index');
    Route::post('image', 'ImageController@store');
    Route::post('image/{id}', 'ImageController@update');
    Route::get('image/{id}', 'ImageController@show');
    Route::delete('image/{delete}', 'ImageController@delete')->where('delete', 'delete');
    Route::delete('image/{id}', 'ImageController@destroy');
});
