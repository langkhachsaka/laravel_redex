<?php

namespace Modules\CourierCompany\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Base\Models\BaseModel;
use Modules\BillOfLading\Models\BillOfLading;

class CourierCompany extends BaseModel
{
    protected $fillable = [
        'name'
    ];

    public function billOfLading()
    {
        return $this->hasMany(BillOfLading::class);
    }
}
