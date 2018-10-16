<?php

namespace Modules\AreaCode\Models;

use Modules\Base\Models\BaseModel;
use Modules\Customer\Models\CustomerAddress;
use Modules\CustomerOrder\Models\CustomerOrder;

class AreaCode extends BaseModel
{
    protected $fillable = [
        'name',
        'code',
        'delivery_fee_unit',
    ];

    public function customerAddress()
    {
        return $this->belongsTo(CustomerAddress::class);
    }
    public function customerOrder()
    {
        return $this->belongsTo(CustomerOrder::class);
    }
}
