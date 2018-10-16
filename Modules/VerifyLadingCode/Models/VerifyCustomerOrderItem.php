<?php

namespace Modules\VerifyLadingCode\Models;

use Modules\Base\Models\BaseModel;
use Modules\CustomerOrder\Models\CustomerOrderItem;
use Modules\Image\Models\Image;
use Modules\LadingCode\Models\LadingCode;
use Modules\User\Models\User;

class VerifyCustomerOrderItem extends BaseModel
{

    protected $fillable = [
        'verify_lading_code_id',
        'customer_order_item_id',
        'lading_code',
        'quantity_verify',
        'is_broken_gash',
        'is_error_size',
        'is_error_color',
        'is_error_product',
        'is_exuberancy',
        'is_inadequate',
        'image1',
        'image2',
        'image3',
        'image4',
        'image5',
        'note',
        'lading_code'
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

    public function customerOrderItem(){
        return $this->belongsTo(CustomerOrderItem::class,'customer_order_item_id');
    }
}
