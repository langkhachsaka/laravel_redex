<?php

namespace Modules\Rate\Models;

use Modules\Base\Models\BaseModel;

class Rate extends BaseModel
{
    protected $fillable = [
        'date',
        'buying_rate',
        'transfer_rate',
        'payment_rate',
        'order_rate'
    ];

    public static function lastOrderRate()
    {
        return self::orderBy('date', 'desc')->value('order_rate');
    }
}
