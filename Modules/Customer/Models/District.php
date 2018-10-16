<?php

namespace Modules\Customer\Models;

use Modules\Base\Models\BaseModel;

class District extends BaseModel
{
    protected $table = 'devvn_quanhuyen';
    protected $fillable = [
        'maqh',
        'name',
        'type',
        'matp',
    ];

}
