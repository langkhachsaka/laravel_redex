<?php
/**
 * Created by PhpStorm.
 * User: NguyenTri
 * Date: 10/05/2018
 * Time: 10:12 SA
 */

namespace Modules\Delivery\Models;

use Modules\Base\Models\BaseModel;
use Modules\LadingCode\Models\LadingCode;
use Modules\User\Models\User;

class Delivery extends BaseModel
{

    const STATUS_PENDING = 0;
    const STATUS_PROCESSED = 1;

    protected $fillable = [
        'date_delivery',
        'note',
        'user_id',
        'transaction_id',
        'status',
        'image'
    ];
    protected $appends = [
        'status_name'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function getStatusNameAttribute()
    {
        $status = [
            self::STATUS_PENDING => 'Chưa giao hàng',
            self::STATUS_PROCESSED => 'Đã nhận hàng',
        ];

        return data_get($status, $this->status);
    }


}