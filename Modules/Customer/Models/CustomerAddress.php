<?php

namespace Modules\Customer\Models;

use Modules\AreaCode\Models\AreaCode;
use Modules\Base\Models\BaseModel;
use Modules\BillOfLading\Models\BillOfLading;

class CustomerAddress extends BaseModel
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'customer_id',
        'is_default',
        'area_code_id',
        'provincial_id',
        'district_id',
        'ward_id',
    ];

    protected $appends = [
        'full_address'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function billOfLading()
    {
        return $this->hasMany(BillOfLading::class);
    }

    public function areaCode()
    {
        return $this->belongsTo(AreaCode::class);
    }

    public function provincial()
    {
        return $this->hasOne(Provincial::class, 'matp', 'provincial_id');
    }

    public function district()
    {
        return $this->hasOne(District::class, 'maqh', 'district_id');
    }

    public function ward()
    {
        return $this->hasOne(Ward::class, 'xaid', 'ward_id');
    }


    public function getFullAddressAttribute()
    {
        $this->load(['provincial', 'district', 'ward']);

        $address = $this->address;

        if ($this->ward) {
            $address .= ', ' . $this->ward->name;
        }

        if ($this->district) {
            $address .= ', ' . $this->district->name;
        }

        if ($this->provincial) {
            $address .= ', ' . $this->provincial->name;
        }

        return $address;
    }

}
