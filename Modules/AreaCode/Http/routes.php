<?php

Route::group(['middleware' => 'auth:api', 'prefix' => 'api/v1', 'namespace' => 'Modules\AreaCode\Http\Controllers'], function()
{
    Route::get('area-code', 'AreaCodeController@index');
    Route::get('area-code/list', 'AreaCodeController@list');
    Route::post('area-code', 'AreaCodeController@store');
    Route::post('area-code/{id}', 'AreaCodeController@update');
    Route::get('area-code/{id}', 'AreaCodeController@show');
    Route::delete('area-code/{id}', 'AreaCodeController@destroy');
});
