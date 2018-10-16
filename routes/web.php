<?php
use Illuminate\Http\Request;
use Modules\Rate\Models\Rate;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('customer.login');
})->middleware('customer_guest');

Auth::routes();

Route::get('home', 'HomeController@index')->name('home');

Route::get('/check-login',function (){
    return response()->json([
        'login' => auth('customer')->check()
    ]);
});

Route::get('/get-rate','ExtensionController@getRate');

Route::get('/item-receiver',function (){
    return view('inner');
});

Route::post('/item-receiver','ExtensionController@itemReceiver');

Route::get('/update-session','ExtensionController@updateSession');

Route::group(['prefix' => 'customer'], function (){
    Route::group(['middleware' => 'customer_guest'], function (){
        Route::get('login','Customer\AuthController@getLogin');
        Route::post('login','Customer\AuthController@postLogin');
        Route::get('register','Customer\AuthController@getRegister');
        Route::post('register','Customer\AuthController@postRegister');
        Route::get('password/forgot','Customer\AuthController@getForgotPass');
        Route::post('password/forgot','Customer\AuthController@sendResetLinkEmail');
        Route::get('/password/reset/{token}','Customer\ResetPasswordController@showResetForm')->name('customer.password.request');
        Route::post('/password/reset','Customer\ResetPasswordController@reset')->name('customer.password.reset');
    });
    Route::group(['middleware' => 'customer'], function (){
        Route::get('logout','Customer\AuthController@logout');

        // order
        Route::resource('/order', 'Customer\OrderController');
        Route::get('order/view/{id}',['as' => 'order.view', 'uses'=>'Customer\OrderController@show']);
        Route::post('order/create', ['as'=>'order.create', 'uses'=>'Customer\OrderController@store']);
        Route::get('order/edit/{id}',['as'=>'order.edit', 'uses'=>'Customer\OrderController@edit']);
        Route::post('order/edit/{id}',['as'=>'order.update','uses'=>'Customer\OrderController@update']);
        Route::get('order/delete/{id}',['as' => 'order.delete', 'uses'=>'Customer\OrderController@delete']);
        Route::post('order/deleteitem', 'Customer\OrderController@deleteItem');
        Route::post('order/upload','Customer\OrderController@upload');
        Route::post('order/import',['as' => 'order.import', 'uses' => 'Customer\OrderController@import']);
        Route::post('order/upload-image', 'Customer\OrderController@uploadImage');
        Route::post('order/get-excel', ['as'=> 'order.get-excel','uses'=>'Customer\OrderController@getExcel']);
        Route::get('order/get-complaint/{id}', ['as' => 'order.get-complaint', 'uses' => 'Customer\OrderController@getComplaint']);
        Route::post('order/deposit','Customer\OrderController@deposit');
        Route::post('order/delete-orders',['as' => 'orders.delete', 'uses' => 'Customer\OrderController@deleteOrders']);
        Route::post('order/deposit-orders',['as' => 'orders.deposit', 'uses' => 'Customer\OrderController@depositOrders']);
        Route::post('order/payment',['as' => 'order.payment', 'uses' => 'Customer\OrderController@payment']);

        // order item
        Route::post('order-item/create',['as' => 'order-item.create', 'uses' => 'Customer\OrderItemsController@store']);
        Route::post('order-item/edit/{id}',['as'=>'order-item.update','uses'=>'Customer\OrderItemsController@update']);
        Route::get('order-item/confirm-not-enough-product/{id}',['as'=>'order-item.confirm','uses'=>'Customer\OrderItemsController@confirmNotEnoughProduct']);
        Route::get('order-item/remove-not-enough-product/{id}',['as'=>'order-item.remove','uses'=>'Customer\OrderItemsController@removeItemNotEnoughProduct']);
        Route::post('order-item/validate',['as'=>'order-item.validate','uses'=>'Customer\OrderItemsController@validateItem']);
        Route::get('order-item/delete/{id}',['as' => 'order-item.delete','uses'=>'Customer\OrderItemsController@delete']);

        // address
        Route::resource('/address', 'Customer\AddressController');
        Route::post('address/create', ['as'=>'address.create', 'uses'=>'Customer\AddressController@store']);
        Route::post('address/create-ajax', 'Customer\AddressController@addNewAddress');
        Route::get('address/edit/{id}',['as'=>'address.edit', 'uses'=>'Customer\AddressController@edit']);
        Route::post('address/edit/{id}',['as'=>'address.update','uses'=>'Customer\AddressController@update']);
        Route::get('address/delete/{id}',['as'=>'address.delete', 'uses'=>'Customer\AddressController@delete']);
        Route::post('address/get-phone','Customer\AddressController@getPhone');

        //order transport
        Route::resource('/order-transport','Customer\OrderTransportController');
        Route::post('/order-transport/create',['as' => 'order-transport.create', 'uses' => 'Customer\OrderTransportController@store']);
        Route::get('/order-transport/delete/{id}',['as' => 'order-transport.delete', 'uses'=>'Customer\OrderTransportController@delete']);
        Route::get('/order-transport/edit/{id}',['as'=>'order-transport.edit','uses'=>'Customer\OrderTransportController@edit']);
        Route::post('/order-transport/edit/{id}',['as'=>'order-transport.update','uses'=>'Customer\OrderTransportController@update']);
        Route::get('/order-transport/get-complaint/{id}', ['as' => 'order-transport.get-complaint', 'uses' => 'Customer\OrderTransportController@getComplaint']);
        Route::get('/order-transport/view/{id}',['as' => 'order-transport.view', 'uses'=>'Customer\OrderTransportController@show']);

        // complaint
        Route::resource('/complaint','Customer\ComplaintController');
        Route::post('/complaint/create',['as' => 'complaint.create', 'uses' => 'Customer\ComplaintController@store']);
        Route::get('/complaint/view/{id}',['as' => 'complaint.view', 'uses'=>'Customer\ComplaintController@show']);
        Route::get('/complaint/delete/{id}',['as' => 'complaint.delete', 'uses'=>'Customer\ComplaintController@delete']);

        // info
        Route::get('/info', ['as' => 'customer.info', 'uses' => 'Customer\InfoController@info']);
        Route::post('/info', ['as' => 'customer.info.update', 'uses' => 'Customer\InfoController@update']);

        //wallet
        Route::get('wallet/',['as' => 'wallet.index', 'uses'=> 'Customer\WalletController@index']);
        Route::post('/wallet/withdrawal',['as'=>'withdrawal.create','uses'=>'Customer\WalletController@withdrawal']);
        Route::get('/wallet/recharge',['as'=>'wallet.recharge','uses'=>'Customer\WalletController@recharge']);

        Route::post('/update-address','Customer\AddressController@updateAjax');

        //deposit
        Route::get('deposit','Customer\DepositController@index');

        //nhận hàng
        Route::get('receive','Customer\ReceiveController@index');
        Route::get('/lading-code/bill','Customer\ReceiveController@bill');
        Route::post('/bill/create','Customer\ReceiveController@store');
    });
});

Route::get('admin', function () {
    return view('welcome');
});

Route::get('admin/{any}', function () {
    return view('welcome');
})->where(['any' => '.*']);

Route::get('get-tinh-thanh-pho', function () {
    $provinces = \DB::table('devvn_tinhthanhpho')->get();

    return response()->json([
        'data' => $provinces
    ]);
});

Route::get('get-quan-huyen',function (Request $request){
    $matp = $request->matp;
    $district = \DB::table('devvn_quanhuyen')->where('matp',$matp)->get();

    return response()->json([
        'data' => $district
    ]);
});

Route::get('get-phuong-xa',function (Request $request){
    $maqh = $request->maqh;
    $ward = \DB::table('devvn_xaphuongthitran')->where('maqh',$maqh)->get();

    return response()->json([
        'data' => $ward
    ]);
});

Route::get('get-address',function (Request $request){
    $id = $request->id;
    $address = \DB::table('customer_addresses')->where('id',$id)->first();
    return response()->json([
        'data' => $address
    ]);
});

Route::post('get-total-amount', 'Customer\OrderController@getTotalAmountNeedPayment');
