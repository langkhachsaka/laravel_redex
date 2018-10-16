<?php

Route::group(['middleware' => 'web', 'prefix' => 'base', 'namespace' => 'Modules\Base\Http\Controllers'], function()
{
    Route::get('/', 'BaseController@index');
});
