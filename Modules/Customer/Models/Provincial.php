<?php

namespace Modules\Customer\Models;

use Modules\Base\Models\BaseModel;

class Provincial extends BaseModel
{
    protected $table = 'devvn_tinhthanhpho';
    protected $fillable = [
        'matp',
        'name',
        'type',
    ];

}
