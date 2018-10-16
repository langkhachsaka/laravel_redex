<?php

namespace Modules\Warehouse\Models;

use Modules\Base\Models\BaseModel;
use Modules\User\Models\User;

class Warehouse extends BaseModel
{
    const TYPE_VIETNAM = 1;
    const TYPE_CHINA = 2;

    protected $fillable = [
        'name',
        'address',
        'type'
    ];

    protected $appends = [
        'type_name'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function getTypeNameAttribute()
    {
        $types = [
            1 => 'Kho Việt Nam',
            2 => 'Kho Trung Quốc'
        ];
        return data_get($types, $this->type);
    }
}
