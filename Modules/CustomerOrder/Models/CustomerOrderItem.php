<?php

namespace Modules\CustomerOrder\Models;

use Modules\Base\Models\BaseModel;
use Modules\ChinaOrder\Models\ChinaOrderItem;
use Modules\Image\Models\Image;
use Modules\LadingCode\Models\LadingCode;
use Modules\Setting\Models\Setting;
use Modules\Shop\Models\Shop;
use Modules\VerifyLadingCode\Models\VerifyCustomerOrderItem;

class CustomerOrderItem extends BaseModel
{
    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;
    const STATUS_TRADING = 2;
    const STATUS_TRADED = 3;
    const STATUS_COMPLAINT = 4;
    const STATUS_FINISHED =5;

    protected $fillable = [
        'image',
        'link',
        'description',
        'size',
        'colour',
        'unit',
        'note',
        'quantity',
        'status',
        'price_cny',
        'shop_id',
        'customer_order_id',
        'weight',
        'volume',
        'alerted', // =0 not yet alert, =1 alerted, =2 : customer confirmed
        'shop_quantity',
        'discount_percent',
        'discount_price',
        'discount_formality',
        'discount_customer_percent',
        'discount_customer_price',
        'surcharge', // phụ phí
    ];

    protected $appends = [
        'status_name',
        'total_price',
        'total_weight',
        'discount_link',
    ];

    public function customerOrder()
    {
        return $this->belongsTo(CustomerOrder::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imagetable');
    }

    public function chinaOrderItem()
    {
        return $this->hasOne(ChinaOrderItem::class);
    }

    public function ladingCodes()
    {
        return $this->morphMany(LadingCode::class, 'ladingcodetable');
    }

    public function verifyCustomerOrderItem(){
        return $this->hasOne(VerifyCustomerOrderItem::class);
    }

    public function getStatusNameAttribute()
    {
        $status = [
            self::STATUS_PENDING => 'Chờ duyệt',
            self::STATUS_APPROVED => 'Đã duyệt mua',
            self::STATUS_TRADING => 'Đang g.dịch',
            self::STATUS_TRADED => 'G.dịch xong',
            self::STATUS_COMPLAINT => 'Khiếu nại',
            self::STATUS_FINISHED => 'Hoàn thành',
        ];
        return data_get($status, $this->status);
    }

    public function getTotalPriceAttribute()
    {
        return $this->price_cny * $this->quantity;
    }

    public function getTotalWeightAttribute()
    {
        return $this->weight * $this->quantity;
    }

    public function getDiscountLinkAttribute()
    {
        $discountLink = Setting::query()->pluck('discount_link')->first();

        return $discountLink . $this->description;
    }

}
