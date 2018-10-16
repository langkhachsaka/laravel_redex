<?php

namespace Modules\Setting\Models;

use Modules\Base\Models\BaseModel;

class Setting extends BaseModel
{
    protected $fillable = [
        'error_weight',
        'error_size',
        'error_type',
        'status',
        'rate',
        'discount_link',
        'order_deposit_percent',
        'factor_conversion',
    ];

    public static function getValue($key) {
        return self::query()->pluck($key)->first();
    }
}
