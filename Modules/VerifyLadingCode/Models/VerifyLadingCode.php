<?php

namespace Modules\VerifyLadingCode\Models;

use Modules\Base\Models\BaseModel;
use Modules\Image\Models\Image;
use Modules\LadingCode\Models\LadingCode;
use Modules\User\Models\User;

class VerifyLadingCode extends BaseModel
{
    const NO_PROBLEM = 0;
    const HAVE_PROBLEM = 1;
    const NOT_YET_VERIFY = 2;
    const DISABLED = 4;
    const TYPE_1_1 = 1;
    const TYPE_1_MANY = 2;

    const TYPE_SUB_LADING_CODE = 3;
    protected $fillable = [
        'lading_code',
        'verifier_id',
        'status',
        'type',
        'weight',
        'height',
        'width',
        'length'
    ];
    protected $appends = [
        'status_name'
    ];
    public function user()
    {
        return $this->belongsTo(User::class,'verifier_id','id')->select('id','name','username');
    }

    public function getStatusNameAttribute()
    {
        $status = [
            self::HAVE_PROBLEM => 'Hàng không nguyên vẹn',
            self::NO_PROBLEM => 'Hàng nguyên vẹn',
            self::NOT_YET_VERIFY => 'Chưa kiểm tra',
        ];
        return data_get($status, $this->status);
    }
 /*   public function images()
    {
        return $this->morphMany(Image::class, 'imagetable');
    }*/
    public function ladingCodeItem()
    {
        return $this->belongsTo(LadingCode::class,'lading_code','code');
    }

}
