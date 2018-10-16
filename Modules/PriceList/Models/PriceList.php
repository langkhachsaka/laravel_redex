<?php

namespace Modules\PriceList\Models;

use Modules\Base\Models\BaseModel;

class PriceList extends BaseModel
{
    protected $fillable = [
        'type',
        'price',
        'delivery_type',
        'is_whosale'
    ];

}
