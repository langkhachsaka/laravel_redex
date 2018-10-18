<?php

namespace Modules\Auth\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Modules\AreaCode\Models\AreaCode;
use Modules\Base\Http\Controllers\Controller;
use Modules\BillOfLading\Models\BillOfLading;
use Modules\ChinaOrder\Models\ChinaOrder;
use Modules\ChinaOrder\Models\ChinaOrderItem;
use Modules\Complaint\Models\Complaint;
use Modules\Complaint\Models\ComplaintHistory;
use Modules\CourierCompany\Models\CourierCompany;
use Modules\Customer\Models\Customer;
use Modules\CustomerOrder\Models\CustomerOrder;
use Modules\CustomerOrder\Models\CustomerOrderItem;
use Modules\Delivery\Models\Delivery;
use Modules\Inventory\Models\Inventory;
use Modules\LadingCode\Models\LadingCode;
use Modules\PriceList\Models\PriceList;
use Modules\Setting\Models\Setting;
use Modules\Shop\Models\Shop;
use Modules\Statistical\Models\Statistical;
use Modules\Transaction\Models\Transaction;
use Modules\User\Models\User;
use Modules\User\Models\UserRole;
use Modules\Warehouse\Models\Warehouse;
use Modules\WarehouseReceivingCN\Models\WarehouseReceivingCN;
use Modules\WarehouseReceivingVN\Models\WarehouseReceivingVN;
use Modules\Rate\Models\Rate;
use Modules\Blog\Models\Blog;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['username', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Thông tin đăng nhập không chính xác',
            ], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return $this->respondSuccessData([], 'Đăng xuất thành công');
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        $user = auth()->user();
        $user->load('warehouse');
        $userRoles = UserRole::where('user_id',auth()->user()->id)->get();
        $roles = [];
        foreach ($userRoles as $role){
            array_push($roles,$role->role);
        }
        unset($user->role);
        $user->roles = $roles;
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => $user,
            'permissions' => $this->getUserPermissions(),
            'message' => 'Đăng nhập thành công',
        ]);
    }

    protected function getUserPermissions()
    {
        return [
            'area_code' => [
                'index' => auth()->user()->can('index', AreaCode::class),
                'view' => auth()->user()->can('view', AreaCode::class),
                'create' => auth()->user()->can('create', AreaCode::class),
                'update' => auth()->user()->can('update', AreaCode::class),
                'delete' => auth()->user()->can('delete', AreaCode::class),
            ],
            'bill_of_lading' => [
                'index' => auth()->user()->can('index', BillOfLading::class),
                'view' => auth()->user()->can('view', BillOfLading::class),
                'create' => auth()->user()->can('create', BillOfLading::class),
                'update' => auth()->user()->can('update', BillOfLading::class),
                'delete' => auth()->user()->can('delete', BillOfLading::class),
                'approve' => auth()->user()->can('approve', BillOfLading::class),
                'form_disabled' => [
                    'seller_id' => auth()->user()->cant('chooseSellerId', BillOfLading::class)
                ],
                'search_disabled' => [
                    'seller_id' => auth()->user()->cant('searchBySellerId', BillOfLading::class)
                ],
            ],
            'china_order' => [
                'index' => auth()->user()->can('index', ChinaOrder::class),
                'view' => auth()->user()->can('view', ChinaOrder::class),
                'create' => auth()->user()->can('create', ChinaOrder::class),
                'update' => auth()->user()->can('update', ChinaOrder::class),
                'delete' => auth()->user()->can('delete', ChinaOrder::class),
                'approve' => auth()->user()->can('approve', ChinaOrder::class),
                'form_disabled' => [
                    'user_purchasing_id' => auth()->user()->cant('chooseUserPurchasingId', ChinaOrder::class)
                ],
                'search_disabled' => [
                    'user_purchasing_id' => auth()->user()->cant('searchByUserPurchasingId', ChinaOrder::class)
                ],
            ],
            'china_order_item' => [
                'view' => auth()->user()->can('view', ChinaOrderItem::class),
                'create' => auth()->user()->can('create', ChinaOrderItem::class),
                'update' => auth()->user()->can('update', ChinaOrderItem::class),
                'delete' => auth()->user()->can('delete', ChinaOrderItem::class)
            ],
            'complaint' => [
                'index' => auth()->user()->can('index', Complaint::class),
                'view' => auth()->user()->can('view', Complaint::class),
                'create' => auth()->user()->can('create', Complaint::class),
                'update' => auth()->user()->can('update', Complaint::class),
                'delete' => auth()->user()->can('delete', Complaint::class),
            ],
            'complaint_history' => [
                'index' => auth()->user()->can('index', ComplaintHistory::class),
                'view' => auth()->user()->can('view', ComplaintHistory::class),
                'create' => auth()->user()->can('create', ComplaintHistory::class),
                'update' => auth()->user()->can('update', ComplaintHistory::class),
                'delete' => auth()->user()->can('delete', ComplaintHistory::class),
            ],
            'courier_company' => [
                'index' => auth()->user()->can('index', CourierCompany::class),
                'view' => auth()->user()->can('view', CourierCompany::class),
                'create' => auth()->user()->can('create', CourierCompany::class),
                'update' => auth()->user()->can('update', CourierCompany::class),
                'delete' => auth()->user()->can('delete', CourierCompany::class),
            ],
            'customer' => [
                'index' => auth()->user()->can('index', Customer::class),
                'view' => auth()->user()->can('view', Customer::class),
                'create' => auth()->user()->can('create', Customer::class),
                'update' => auth()->user()->can('update', Customer::class),
                'delete' => auth()->user()->can('delete', Customer::class),
                'form_update_disabled' => [
                    'password' => auth()->user()->cant('updatePassword', Customer::class)
                ],
            ],
            'customer_order' => [
                'index' => auth()->user()->can('index', CustomerOrder::class),
                'view' => auth()->user()->can('view', CustomerOrder::class),
                'create' => auth()->user()->can('create', CustomerOrder::class),
                'update' => auth()->user()->can('update', CustomerOrder::class),
                'delete' => auth()->user()->can('delete', CustomerOrder::class),
                'approve' => auth()->user()->can('approve', CustomerOrder::class),
                'form_disabled' => [
                    'seller_id' => auth()->user()->cant('updateSellerId', CustomerOrder::class)
                ],
                'search_disabled' => [
                    'seller_id' => auth()->user()->cant('searchBySellerId', CustomerOrder::class)
                ],
            ],
            'customer_order_item' => [
                'view' => auth()->user()->can('view', CustomerOrderItem::class),
                'create' => auth()->user()->can('create', CustomerOrderItem::class),
                'update' => auth()->user()->can('update', CustomerOrderItem::class),
                'delete' => auth()->user()->can('delete', CustomerOrderItem::class)
            ],
            'delivery' => [
                'index' => auth()->user()->can('index', Delivery::class),
                'view' => auth()->user()->can('view', Delivery::class),
                'create' => auth()->user()->can('create', Delivery::class),
                'update' => auth()->user()->can('update', Delivery::class),
                'delete' => auth()->user()->can('delete', Delivery::class)
            ],
            'inventory' => [
                'index' => auth()->user()->can('index', Inventory::class),
                'view' => auth()->user()->can('view', Inventory::class),
                'create' => auth()->user()->can('create', Inventory::class),
                'update' => auth()->user()->can('update', Inventory::class),
                'delete' => auth()->user()->can('delete', Inventory::class),
            ],
            'shop' => [
                'index' => auth()->user()->can('index', Shop::class),
                'create' => auth()->user()->can('create', Shop::class),
                'update' => auth()->user()->can('update', Shop::class),
                'delete' => auth()->user()->can('delete', Shop::class),
            ],
            'user' => [
                'index' => auth()->user()->can('index', User::class),
                'view' => auth()->user()->can('view', User::class),
                'create' => auth()->user()->can('create', User::class),
                'update' => auth()->user()->can('update', User::class),
                'delete' => auth()->user()->can('delete', User::class),
            ],
            'warehouse' => [
                'index' => auth()->user()->can('index', Warehouse::class),
                'view' => auth()->user()->can('view', Warehouse::class),
                'create' => auth()->user()->can('create', Warehouse::class),
                'update' => auth()->user()->can('update', Warehouse::class),
                'delete' => auth()->user()->can('delete', Warehouse::class),
            ],
            'warehouse_receiving_cn' => [
                'index' => auth()->user()->can('index', WarehouseReceivingCN::class),
                'view' => auth()->user()->can('view', WarehouseReceivingCN::class),
                'create' => auth()->user()->can('create', WarehouseReceivingCN::class),
                'update' => auth()->user()->can('update', WarehouseReceivingCN::class),
                'delete' => auth()->user()->can('delete', WarehouseReceivingCN::class),
            ],
            'task' => [
                'index' => true,
                'view' => true,
                'create' => true,
                'update' => true,
                'delete' => true,
            ],
            'warehouse_receiving_vn' => [
                'index' => auth()->user()->can('index', WarehouseReceivingVN::class),
                'view' => auth()->user()->can('view', WarehouseReceivingVN::class),
                'create' => auth()->user()->can('create', WarehouseReceivingVN::class),
                'update' => auth()->user()->can('update', WarehouseReceivingVN::class),
                'delete' => auth()->user()->can('delete', WarehouseReceivingVN::class),
            ],
            'statistical' => [
                'index' => auth()->user()->can('index', Statistical::class),
                'view' => true,
                'create' => true,
                'update' => true,
                'delete' => true,
            ],
            'notification' => [
                'index' => true,
                'view' => true,
                'create' => true,
                'update' => true,
                'delete' => true,
            ],
            'transaction' => [
                'index' => auth()->user()->can('index', Transaction::class),
                'view' => auth()->user()->can('view', Transaction::class),
                'create' => auth()->user()->can('create', Transaction::class),
                'update' => auth()->user()->can('update', Transaction::class),
                'delete' => auth()->user()->can('delete', Transaction::class),
            ],
            'rate' => [
                'index' => auth()->user()->can('index', Rate::class),
                'create' => auth()->user()->can('create', Rate::class),
                'update' => auth()->user()->can('update', Rate::class),
                'delete' => auth()->user()->can('delete', Rate::class),
            ],
            'setting' => [
                'index' => auth()->user()->can('index', Setting::class),
                'update' => auth()->user()->can('update', Setting::class),
            ],
            'ladingCode' => [
                'index' => auth()->user()->can('index', LadingCode::class),
                'view' => auth()->user()->can('view', LadingCode::class),
                'create' => auth()->user()->can('create', LadingCode::class),
                'update' => auth()->user()->can('update', LadingCode::class),
                'delete' => auth()->user()->can('delete', LadingCode::class),
            ],
            'tracking' =>[
                'index' => true,
                'view' => true,
                'create' => true,
                'update' => true,
                'delete' => true,
            ],
            'priceList' => [
                'index' => auth()->user()->can('index', PriceList::class),
                'view' => auth()->user()->can('view', PriceList::class),
                'create' => auth()->user()->can('create', PriceList::class),
                'update' => auth()->user()->can('update', PriceList::class),
                'delete' => auth()->user()->can('delete', PriceList::class),
            ],
            'blog' =>[
                'index' => auth()->user()->can('index', Blog::class),
                'view' => auth()->user()->can('view', Blog::class),
                'create' => auth()->user()->can('create', Blog::class),
                'update' => auth()->user()->can('update', Blog::class),
                'delete' => auth()->user()->can('delete', Blog::class),
                
            ],
        ];
    }
}
