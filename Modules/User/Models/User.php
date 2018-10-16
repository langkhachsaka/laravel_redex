<?php

namespace Modules\User\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Modules\Base\Models\QueryHelper;
use Modules\BillOfLading\Models\BillOfLading;
use Modules\Complaint\Models\Complaint;
use Modules\CustomerOrder\Models\CustomerOrder;
use Modules\Delivery\Models\Delivery;
use Modules\Transaction\Models\Transaction;
use Modules\Warehouse\Models\Warehouse;
use Tymon\JWTAuth\Contracts\JWTSubject as JWTSubjectContract;

class User extends Authenticatable implements JWTSubjectContract
{
    use Notifiable;
    use QueryHelper;

    const WAREHOUSE_STAFF_VN = 33;
    const WAREHOUSE_STAFF_CN = 32;

    const ROLE_ADMIN = 10;

    const ROLE_CUSTOMER_SERVICE_MANAGEMENT = 20;
    const ROLE_ORDERING_MANAGEMENT = 21;
    const ROLE_DELIVERING_AND_RECEIVING_MANAGEMENT = 22;

    const ROLE_CUSTOMER_SERVICE_OFFICER = 30;
    const ROLE_ORDERING_SERVICE_OFFICER = 31;
    const ROLE_CHINESE_SHIPPING_OFFICER = 32;
    const ROLE_VIETNAMESE_SHIPPING_OFFICER = 33;
    const ROLE_ACCOUNTANT = 40;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'username',
        'phone',
        'email',
        'role',
        'warehouse_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function customerOrder()
    {
        return $this->hasMany(CustomerOrder::class, 'seller_id');
    }

    public function userRoles()
    {
        return $this->hasMany(UserRole::class, 'user_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function billOfLading()
    {
        return $this->hasMany(BillOfLading::class);
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }

    public function complaint()
    {
        return $this->hasMany(Complaint::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }


    public function isAdmin()
    {
        return UserRole::where('user_id',$this->id)->where('role',10)->exists();
//        return $this->role === 10;
    }

    /**
     * Quản lý chăm sóc khách hàng
     */
    public function isCustomerServiceManagement()
    {
        return UserRole::where('user_id',$this->id)->where('role',20)->exists();
//        return $this->role === 20;
    }

    /**
     * Quản lý đặt hàng
     */
    public function isOrderingManagement()
    {
        return UserRole::where('user_id',$this->id)->where('role',21)->exists();
//        return $this->role === 21;
    }

    /**
     * Quản lý bộ phận giao nhận
     */
    public function isDeliveringAndReceivingManagement()
    {
        return UserRole::where('user_id',$this->id)->where('role',22)->exists();
//        return $this->role === 22;
    }

    /**
     * Nhân viên chăm sóc khách hàng
     */
    public function isCustomerServiceOfficer()
    {
        return UserRole::where('user_id',$this->id)->where('role',30)->exists();
//        return $this->role === 30;
    }

    /**
     * Nhân viên đặt hàng
     */
    public function isOrderingOfficer()
    {
        return UserRole::where('user_id',$this->id)->where('role',31)->exists();
//        return $this->role === 31;
    }

    /**
     * Nhân viên giao nhận Trung Quốc
     */
    public function isChineseShippingOfficer()
    {
        return UserRole::where('user_id',$this->id)->where('role',32)->exists();
//        return $this->role === 32;
    }

    /**
     * Nhân viên giao nhận Việt Nam
     */
    public function isVietnameseShippingOfficer()
    {
        return UserRole::where('user_id',$this->id)->where('role',33)->exists();
//        return $this->role === 33;
    }

    /**
     * Nhân viên kế toán
     */
    public function isAccountant()
    {
        return UserRole::where('user_id',$this->id)->where('role',40)->exists();
//        return $this->role === 40;
    }

    /*
     * VCL test
     */
    public function isVCL()
    {
        return UserRole::where('user_id',$this->id)->where('role',1)->exists();
//        return $this->role === 1;
    }
}
