<?php

namespace Modules\User\Models;

use Modules\Base\Models\BaseModel;

class UserRole extends BaseModel
{

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
        'user_id',
        'role',
    ];

    protected $appends = [
        'role_name'
    ];


    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */


    public function getRoleNameAttribute()
    {
        $roles = [
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_CUSTOMER_SERVICE_MANAGEMENT => "Quản lý CSKH",
            self::ROLE_ORDERING_MANAGEMENT => "Quản lý đặt hàng",
            self::ROLE_DELIVERING_AND_RECEIVING_MANAGEMENT => "Quản lý bộ phận giao nhận",
            self::ROLE_CUSTOMER_SERVICE_OFFICER => "Nhân viên CSKH",
            self::ROLE_ORDERING_SERVICE_OFFICER => "Nhân viên đặt hàng",
            self::ROLE_CHINESE_SHIPPING_OFFICER => "Nhân viên giao nhận TQ",
            self::ROLE_VIETNAMESE_SHIPPING_OFFICER => "Nhân viên giao nhận VN",
            self::ROLE_ACCOUNTANT => "Kế toán",
        ];
        return data_get($roles, $this->role);
    }

    public function isAdmin()
    {
        return $this->role === 10;
    }

    /**
     * Quản lý chăm sóc khách hàng
     */
    public function isCustomerServiceManagement()
    {
        return $this->role === 20;
    }

    /**
     * Quản lý đặt hàng
     */
    public function isOrderingManagement()
    {
        return $this->role === 21;
    }

    /**
     * Quản lý bộ phận giao nhận
     */
    public function isDeliveringAndReceivingManagement()
    {
        return $this->role === 22;
    }

    /**
     * Nhân viên chăm sóc khách hàng
     */
    public function isCustomerServiceOfficer()
    {
        return $this->role === 30;
    }

    /**
     * Nhân viên đặt hàng
     */
    public function isOrderingOfficer()
    {
        return $this->role === 31;
    }

    /**
     * Nhân viên giao nhận Trung Quốc
     */
    public function isChineseShippingOfficer()
    {
        return $this->role === 32;
    }

    /**
     * Nhân viên giao nhận Việt Nam
     */
    public function isVietnameseShippingOfficer()
    {
        return $this->role === 33;
    }

    /**
     * Nhân viên kế toán
     */
    public function isAccountant()
    {
        return $this->role === 40;
    }

    /*
     * VCL test
     */
    public function isVCL()
    {
        return $this->role === 1;
    }
}
