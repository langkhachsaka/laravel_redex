<?php

namespace Modules\ChinaOrder\Models;

use Modules\Base\Models\BaseModel;
use Modules\CustomerOrder\Models\CustomerOrderItem;
use Modules\Image\Models\Image;

class ChinaOrderItem extends BaseModel
{
    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;

    protected $fillable = [
        'china_order_id',
        'customer_order_item_id',
        'quantity',
        'price_cny',
        'status',
    ];

    protected $appends = [
        'status_name',
        'total_price',
        'total_weight',
    ];


    public function chinaOrder()
    {
        return $this->belongsTo(ChinaOrder::class);
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imagetable');
    }


    public function customerOrderItem()
    {
        return $this->belongsTo(CustomerOrderItem::class);
    }

    public function getStatusNameAttribute()
    {
        $status = [
            0 => 'Chờ duyệt',
            1 => 'Đã duyệt mua',
            2 => 'Đang g.dịch',
            3 => 'G.dịch xong',
            4 => 'Khiếu nại',
            5 => 'Hoàn thành',
        ];
        return data_get($status, $this->status);
    }

    public function getTotalPriceAttribute()
    {
        return $this->customerOrderItem->price_cny * $this->quantity;
    }

    public function getTotalWeightAttribute()
    {
        return $this->customerOrderItem->weight * $this->quantity;
    }

}
