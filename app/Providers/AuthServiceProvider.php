<?php

namespace App\Providers;

use App\Policies\AreaCodePolicy;
use App\Policies\BillOfLadingPolicy;
use App\Policies\ChinaOrderItemPolicy;
use App\Policies\ChinaOrderPolicy;
use App\Policies\ComplaintHistoryPolicy;
use App\Policies\ComplaintPolicy;
use App\Policies\CourierCompanyPolicy;
use App\Policies\CustomerOrderItemPolicy;
use App\Policies\CustomerOrderPolicy;
use App\Policies\CustomerPolicy;
use App\Policies\DeliveryPolicy;
use App\Policies\InventoryPolicy;
use App\Policies\LadingCodePolicy;
use App\Policies\PriceListPolicy;
use App\Policies\RatePolicy;
use App\Policies\SettingPolicy;
use App\Policies\ShopPolicy;
use App\Policies\StatisticalPolicy;
use App\Policies\TransactionPolicy;
use App\Policies\UserPolicy;
use App\Policies\WarehousePolicy;
use App\Policies\WarehouseReceivingCNPolicy;
use App\Policies\WarehouseReceivingVNPolicy;
use App\Policies\BlogPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Modules\AreaCode\Models\AreaCode;
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
use Modules\Rate\Models\Rate;
use Modules\Setting\Models\Setting;
use Modules\Shop\Models\Shop;
use Modules\Statistical\Models\Statistical;
use Modules\Transaction\Models\Transaction;
use Modules\User\Models\User;
use Modules\Warehouse\Models\Warehouse;
use Modules\WarehouseReceivingCN\Models\WarehouseReceivingCN;
use Modules\Blog\Models\Blog;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        AreaCode::class => AreaCodePolicy::class,
        BillOfLading::class => BillOfLadingPolicy::class,
        ChinaOrder::class => ChinaOrderPolicy::class,
        ChinaOrderItem::class => ChinaOrderItemPolicy::class,
        CourierCompany::class => CourierCompanyPolicy::class,
        Customer::class => CustomerPolicy::class,
        CustomerOrder::class => CustomerOrderPolicy::class,
        CustomerOrderItem::class => CustomerOrderItemPolicy::class,
        Delivery::class => DeliveryPolicy::class,
        Inventory::class => InventoryPolicy::class,
        Shop::class => ShopPolicy::class,
        User::class => UserPolicy::class,
        Warehouse::class => WarehousePolicy::class,
        Blog::class => BlogPolicy::class,
        WarehouseReceivingCN::class => WarehouseReceivingCNPolicy::class,
        Complaint::class => ComplaintPolicy::class,
        ComplaintHistory::class => ComplaintHistoryPolicy::class,
        WarehouseReceivingVN::class => WarehouseReceivingVNPolicy::class,
        Transaction::class => TransactionPolicy::class,
        Statistical::class => StatisticalPolicy::class,
        Setting::class => SettingPolicy::class,
        LadingCode::class => LadingCodePolicy::class,
        Rate::class => RatePolicy::class,
        PriceList::class => PriceListPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        //
    }
}
