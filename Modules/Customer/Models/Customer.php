<?php
/**
 * Created by: TriNQ
 * Date: 16-04-2018
 * Time: 15:01 PM
 */

namespace Modules\Customer\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Modules\Base\Models\QueryHelper;
use Modules\BillOfLading\Models\BillOfLading;
use Modules\CustomerOrder\Models\CustomerOrder;
use Illuminate\Notifications\Notifiable;
use App\Notifications\CustomerResetPasswordNotification;
use Modules\Rate\Models\Rate;
use Modules\Setting\Models\Setting;
use Modules\Transaction\Models\Transaction;

class Customer extends Authenticatable
{
    use QueryHelper,Notifiable;
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'rate',
        'wallet'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $appends = [
        'order_rate',
        'order_pre_deposit_percent',
    ];

    public function customerOrders()
    {
        return $this->hasMany(CustomerOrder::class);
    }

    public function customerAddresses()
    {
        return $this->hasMany(CustomerAddress::class);
    }

    public function customerAddress()
    {
        return $this->hasOne(CustomerAddress::class)
            ->orderBy('is_default', 'DESC');
    }

    public function customerAddressDefault()
    {
        return $this->hasOne(CustomerAddress::class)
            ->where('is_default', '=', '1');
    }

    public function billOfLading()
    {
        return $this->hasMany(BillOfLading::class);
    }

    public function complaint()
    {
        return $this->hasMany(Complaint::class);
    }

    public function transaction(){
        return $this->hasMany(Transaction::class);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomerResetPasswordNotification($token));
    }

    public function getOrderRateAttribute()
    {
        if ($this->rate) return $this->rate; // return if customer has rate. end funcion

        $lastRate = Rate::lastOrderRate();

        return $lastRate;
    }

    public function getOrderPreDepositPercentAttribute()
    {
        if ($this->order_deposit_percent) return $this->order_deposit_percent; // return if customer has order_deposit_percent. end funcion

        $orderDepositPercent = Setting::getValue('order_deposit_percent');

        return $orderDepositPercent;
    }

}
