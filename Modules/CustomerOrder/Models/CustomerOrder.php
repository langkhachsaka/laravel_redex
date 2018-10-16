<?php

namespace Modules\CustomerOrder\Models;

use Modules\Base\Models\BaseModel;
use Modules\BillCode\Models\BillCode;
use Modules\ChinaOrder\Models\ChinaOrderItem;
use Modules\Complaint\Models\Complaint;
use Modules\Customer\Models\Customer;
use Modules\Image\Models\Image;
use Modules\LadingCode\Models\LadingCode;
use Modules\Notification\Models\Notification;
use Modules\Transaction\Models\Transaction;
use Modules\User\Models\User;
use Modules\AreaCode\Models\AreaCode;

class CustomerOrder extends BaseModel
{
    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;
    const STATUS_CANCELED = 2;
    const STATUS_PROCESS_DEPOSIT = 3;
    const STATUS_DEPOSITED = 4;
    const STATUS_DELIVERY = 5;
    const STATUS_COMPLAINT = 6;
    const STATUS_FINISHED = 7;

    protected $fillable = [
        'status',
        'customer_id',
        'seller_id',
        'customer_address_id',
        'end_date',
        'customer_billing_name',
        'customer_billing_address',
        'customer_billing_phone',
        'customer_shipping_name',
        'customer_shipping_phone',
        'customer_shipping_address_id',
        'customer_shipping_address',
        'customer_shipping_provincial_id',
        'customer_shipping_district_id',
        'customer_shipping_ward_id',
        'area_code_id',
        'shipping_type',
        // 'money_exchange_rate', // manual set value $model->money_exchange_rate | lưu tỷ giá áp dụng cho đơn hàng
        // 'deposit_percent', // manual set value $model->deposit_percent | lưu % tạm ứng cho đơn hàng
    ];

    protected $appends = [
        'status_name',
        'is_items_updatable',
        'is_approved',
        'can_create_complaint'
    ];

    public function customerOrderItems()
    {
        return $this->hasMany(CustomerOrderItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function areaCode()
    {
        return $this->belongsTo(AreaCode::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id', 'id');
    }

    public function images()
    {
        return $this->hasManyThrough(
            Image::class,
            CustomerOrderItem::class,
            'customer_order_id',
            'imagetable_id',
            'id'
        );
    }

    public function chinaOrderItems()
    {
        return $this->hasMany(ChinaOrderItem::class);
    }

    public function complaint()
    {
        return $this->morphOne(Complaint::class, 'ordertable');
    }

    public function notification()
    {
        return $this->morphMany(Notification::class, 'notificationtable');
    }

    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'transactiontable');
    }

    public function billCodes()
    {
        return $this->hasMany(BillCode::class);
    }

    public function ladingCodes()
    {
        return $this->morphMany(LadingCode::class, 'ladingcodetable');
    }

    public function getStatusNameAttribute()
    {
        $status = [
            self::STATUS_PENDING => 'Chờ duyệt',
            self::STATUS_APPROVED => 'Đã duyệt mua',
            self::STATUS_CANCELED => 'Đã huỷ',
            self::STATUS_PROCESS_DEPOSIT => 'Xử lý đặt cọc',
            self::STATUS_DEPOSITED => 'Đã đặt cọc',
            self::STATUS_DELIVERY => 'Đang giao hàng',
            self::STATUS_COMPLAINT => 'Khiếu nại',
            self::STATUS_FINISHED => 'Hoàn thành',
        ];
        return data_get($status, $this->status);
    }

    public function getIsItemsUpdatableAttribute()
    {
        return !$this->getIsApprovedAttribute();
    }

    public function getIsApprovedAttribute()
    {
        return $this->status !== self::STATUS_PENDING;
    }

    public function getCanCreateComplaintAttribute()
    {
        return true;
    }
}
