<?php

namespace Modules\VerifyLadingCode\Models;

use Modules\Base\Models\BaseModel;
use Modules\CustomerOrder\Models\CustomerOrder;
use Modules\Image\Models\Image;
use Modules\LadingCode\Models\LadingCode;
use Modules\User\Models\User;

class SubLadingCode extends BaseModel
{
    const NO_PROBLEM = 0;
    const HAVE_PROBLEM = 1;

    protected $fillable = [
        'lading_code',
        'sub_lading_code',
        'height',
        'width',
        'length',
        'weight',
        'order_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'verifier_id','id')->select('id','name','username');
    }

 /*   public function images()
    {
        return $this->morphMany(Image::class, 'imagetable');
    }*/
    public function ladingCodeItem()
    {
        return $this->belongsTo(LadingCode::class,'lading_code','code');
    }
    public function customerOrder()
    {
        return $this->belongsTo(CustomerOrder::class,'order_id','id');
    }
    public function ladingCodes()
    {
        return $this->hasMany(LadingCode::class,'code', 'lading_code');
    }
}
