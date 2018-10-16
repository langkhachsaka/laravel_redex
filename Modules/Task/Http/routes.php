<?php

Route::group(
    [
        'middleware' => 'api',
        'prefix' => 'api/v1',
        'namespace' => 'Modules\Task\Http\Controllers'
    ], function() {
    Route::get('task', 'TaskController@index');
    Route::post('task', 'TaskController@store');
    Route::post('task/{id}', 'TaskController@update');
    Route::get('task/{listUser}', 'TaskController@listUser')->where('listUser', '(listUser)');
    Route::get('task/{id}', 'TaskController@show');
    Route::get('task/getInfoOrder/{id}', 'TaskController@getInfoOrder');

  
});
