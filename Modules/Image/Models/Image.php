<?php

namespace Modules\Image\Models;

use Modules\Base\Models\BaseModel;

class Image extends BaseModel
{
    protected $fillable = [
        'path',
        'imagetable_id',
        'imagetable_type'
    ];

    public function imagetable()
    {
        return $this->morphTo();
    }

}
