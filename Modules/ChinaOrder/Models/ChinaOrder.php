<?php

namespace Modules\ChinaOrder\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Base\Models\BaseModel;
use Modules\User\Models\User;
use Modules\Notification\Models\Notification;

class ChinaOrder extends BaseModel
{
    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;
    const STATUS_TRADING = 2;
    const STATUS_TRADED = 3;
    const STATUS_COMPLAINT = 4;
    const STATUS_FINISHED =5;


    protected $fillable = [
        'user_purchasing_id',
        'end_date'
    ];

    protected $appends = [
        'status_name',
        'is_items_updatable',
        'is_approved',
    ];

    public function userPurchasing()
    {
        return $this->belongsTo(User::class, 'user_purchasing_id');
    }

    public function chinaOrderItems()
    {
        return $this->hasMany(ChinaOrderItem::class);
    }

    public function notification()
    {
        return$this->morphMany(Notification::class, 'notificationtable');
    }

    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'transactiontable');
    }

    public function getStatusNameAttribute()
    {
        $status = [
            self::STATUS_PENDING => 'Chờ duyệt',
            self::STATUS_APPROVED => 'Đã duyệt mua',
            self::STATUS_TRADING => 'Đang g.dịch',
            self::STATUS_TRADED => 'G.dịch xong',
            self::STATUS_COMPLAINT => 'Khiếu nại',
            self::STATUS_FINISHED => 'Hoàn thành'
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
}
