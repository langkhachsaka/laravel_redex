<?php

Route::group(['middleware' => 'auth:api', 'prefix' => 'api/v1', 'namespace' => 'Modules\Notification\Http\Controllers'], function()
{
    Route::get('notification', 'NotificationController@index');
    Route::get('notification/list-user-notifications', 'NotificationController@listUserNotifications');
    Route::get('notification/get-sum-notifications-unread', 'NotificationController@getSumNotificationsUnread');
//    Route::post('notification', 'NotificationController@store');
    Route::post('notification/{id}', 'NotificationController@update');
//    Route::get('notification/{id}', 'NotificationController@show');
//    Route::delete('notification/{id}', 'NotificationController@destroy');
});
