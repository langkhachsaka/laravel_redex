<?php

namespace Modules\Shop\Models;

use Modules\Base\Models\BaseModel;
use Modules\BillCode\Models\BillCode;
use Modules\CustomerOrder\Models\CustomerOrderItem;
use Modules\Inventory\Models\Inventory;
use Modules\LadingCode\Models\LadingCode;

class Shop extends BaseModel
{
    protected $fillable = [
        'name',
        'link'
    ];

    public function customerOrderItems()
    {
        return $this->hasMany(CustomerOrderItem::class);
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    public function ladingCodes()
    {
        return $this->hasMany(LadingCode::class);
    }

    public function billCodes()
    {
        return $this->hasMany(BillCode::class);
    }
}
