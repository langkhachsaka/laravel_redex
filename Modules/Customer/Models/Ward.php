<?php

namespace Modules\Customer\Models;

use Modules\Base\Models\BaseModel;

class Ward extends BaseModel
{
    protected $table = 'devvn_xaphuongthitran';
    protected $fillable = [
        'xaid',
        'name',
        'type',
        'maqh',
    ];

}
