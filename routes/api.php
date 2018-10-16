<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});
/*
Route::group(
    [
        'middleware' => 'api',
        'prefix' => 'auth'
    ],
    function ($router) {
        Route::post('login', 'AuthController@login');
        Route::post('logout', 'AuthController@logout');
        Route::post('refresh', 'AuthController@refresh');
        Route::post('me', 'AuthController@me');
    }
);
Route::get('user', 'UserController@index');
Route::post('user', 'UserController@store');
Route::post('user/{id}', 'UserController@update');
Route::get('user/{list}', 'UserController@list')->where('list', '(list)');
Route::get('user/{id}', 'UserController@show');
Route::delete('user/{id}', 'UserController@destroy');

Route::get('shop', 'ShopController@index');
Route::post('shop', 'ShopController@store');
Route::post('shop/{id}', 'ShopController@update');
Route::get('shop/{list}', 'ShopController@list')->where('list', '(list)');
Route::get('shop/{id}', 'ShopController@show');
Route::delete('shop/{id}', 'ShopController@destroy');

Route::get('area-code', 'AreaCodeController@index');
Route::post('area-code', 'AreaCodeController@store');
Route::post('area-code/{id}', 'AreaCodeController@update');
Route::get('area-code/{id}', 'AreaCodeController@show');
Route::delete('area-code/{id}', 'AreaCodeController@destroy');

Route::get('customer', 'CustomerController@index');
Route::post('customer', 'CustomerController@store');
Route::post('customer/{id}', 'CustomerController@update');
Route::get('customer/{list}', 'CustomerController@list')->where('list', '(list)');
Route::get('customer/{id}', 'CustomerController@show');
Route::delete('customer/{id}', 'CustomerController@destroy');
Route::get('customer/{id}/address', 'CustomerController@showAddresses');


Route::get('customer-order', 'CustomerOrderController@index');
Route::post('customer-order', 'CustomerOrderController@store');
Route::post('customer-order/{id}', 'CustomerOrderController@update');
Route::post('customer-order/{id}/approve', 'CustomerOrderController@approve');
Route::get('customer-order/{id}', 'CustomerOrderController@show');
Route::delete('customer-order/{id}', 'CustomerOrderController@destroy');

Route::get('customer-order-item', 'CustomerOrderItemController@index');
Route::post('customer-order-item', 'CustomerOrderItemController@store');
Route::post('customer-order-item/{id}', 'CustomerOrderItemController@update');
Route::get('customer-order-item/{list}', 'CustomerOrderItemController@listAvailable')->where('list', '(list-available)');
Route::get('customer-order-item/{id}', 'CustomerOrderItemController@show');
Route::delete('customer-order-item/{id}', 'CustomerOrderItemController@destroy');

Route::get('image', 'ImageController@index');
Route::post('image', 'ImageController@store');
Route::post('image/{id}', 'ImageController@update');
Route::get('image/{id}', 'ImageController@show');
Route::delete('image/{delete}', 'ImageController@delete')->where('delete', 'delete');
Route::delete('image/{id}', 'ImageController@destroy');

Route::get('bill-of-lading', 'BillOfLadingController@index');
Route::post('bill-of-lading', 'BillOfLadingController@store');
Route::post('bill-of-lading/{id}', 'BillOfLadingController@update');
Route::get('bill-of-lading/{id}', 'BillOfLadingController@show');
Route::delete('bill-of-lading/{id}', 'BillOfLadingController@destroy');

Route::get('china-order', 'ChinaOrderController@index');
Route::post('china-order', 'ChinaOrderController@store');
Route::post('china-order/{id}', 'ChinaOrderController@update');
Route::get('china-order/{id}', 'ChinaOrderController@show');
Route::delete('china-order/{id}', 'ChinaOrderController@destroy');

Route::get('china-order-item', 'ChinaOrderItemController@index');
Route::post('china-order-item', 'ChinaOrderItemController@store');
Route::post('china-order-item/{id}', 'ChinaOrderItemController@update');
Route::get('china-order-item/{id}', 'ChinaOrderItemController@show');
Route::delete('china-order-item/{id}', 'ChinaOrderItemController@destroy');

Route::get('courier-company', 'CourierCompanyController@index');
Route::post('courier-company', 'CourierCompanyController@store');
Route::post('courier-company/{id}', 'CourierCompanyController@update');
Route::get('courier-company/{id}', 'CourierCompanyController@show');
Route::delete('courier-company/{id}', 'CourierCompanyController@destroy');

Route::get('customer-address', 'CustomerAddressController@index');
Route::post('customer-address', 'CustomerAddressController@store');
Route::post('customer-address/{id}', 'CustomerAddressController@update');
Route::get('customer-address/{id}', 'CustomerAddressController@show');
Route::delete('customer-address/{id}', 'CustomerAddressController@destroy');*/
