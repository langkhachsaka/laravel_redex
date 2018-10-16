<?php
/**
 * Created by PhpStorm.
 * User: NguyenTri
 * Date: 30/05/2018
 * Time: 10:17 SA
 */

namespace Modules\Transaction\Models;

use Modules\Base\Models\BaseModel;
use Modules\Customer\Models\Customer;
use Modules\Delivery\Models\Delivery;
use Modules\User\Models\User;

class Transaction extends BaseModel
{
    const TYPE_DEPOSIT = 0;
    const TYPE_WITHDRAWAL = 1;
    const TYPE_RECHARGE = 2;
    const TYPE_REFUND = 3;
    const TYPE_PAYMENT = 4;

    const STT_UNCONFIRMED = 0;
    const STT_CONFIRMED = 1;

    protected $fillable = [
        'transactiontable_id',
        'transactiontable_type',
        'money',
        'note',
        'type',
        'user_id',
        'user_name',
        'customer_id',
        'status'
    ];

    protected $appends = [
        'status_name',
        'type_name'
    ];

    public function transactiontable()
    {
        return $this->morphTo();
    }

    public function delivery(){
        return $this->belongsTo(Delivery::class,'id','transaction_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer(){
        return $this->belongsTo(Customer::class);
    }

    public function paymentInfo(){
        return $this->hasMany(PaymentInformation::class,'transaction_id');
    }


    public function getTypeNameAttribute()
    {
        $type = [
            self::TYPE_DEPOSIT => 'Đặt cọc',
            self::TYPE_WITHDRAWAL => 'Rút tiền',
            self::TYPE_PAYMENT => 'Thanh toán',
            self::TYPE_RECHARGE => 'Nạp tiền'
        ];

        return data_get($type, $this->type);
    }

    public function getStatusNameAttribute()
    {
        $status = [
            self::STT_UNCONFIRMED => 'Chưa xác nhận',
            self::STT_CONFIRMED => 'Đã xác nhận',
        ];

        return data_get($status, $this->status);
    }
}
