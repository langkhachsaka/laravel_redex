<?php
/**
 * Created by PhpStorm.
 * User: NguyenTri
 * Date: 08/06/2018
 * Time: 7:52 SA
 */

namespace Modules\LadingCode\Models;

use Modules\Base\Models\BaseModel;
use Modules\BillCode\Models\BillCode;
use Modules\BillOfLading\Models\BillOfLading;
use Modules\CustomerOrder\Models\CustomerOrder;
use Modules\CustomerOrder\Models\CustomerOrderItem;
use Modules\Shop\Models\Shop;
use Modules\VerifyLadingCode\Models\VerifyLadingCode;
use Modules\WarehouseReceivingVN\Models\WarehouseVnLadingItem;

class LadingCode extends BaseModel
{
    protected $fillable = [
        'ladingcodetable_id',
        'ladingcodetable_type',
        'code',
        'width',
        'height',
        'weight',
        'length',
        'shop_id',
        'bill_code',
    ];

    public function ladingcodetable()
    {
        return $this->morphTo();
    }
    public function billOfLading()
    {
        return $this->belongsTo(BillOfLading::class,'ladingcodetable_id','id');
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function customerOrder()
    {
        return $this->belongsTo(CustomerOrder::class,'ladingcodetable_id','id');
    }

    public function customerOrderItems()
    {
       $query = $this->hasMany(
           CustomerOrderItem::class,
           'customer_order_id',
           'ladingcodetable_id'
       );

       if ($this->shop_id) {
           $query->where('shop_id','=',$this->shop_id);
       } else {
           $query->whereNull('shop_id');
       }

       return $query;
    }

    public function warehouseVnLadingItem()
    {
        return $query = $this->belongsTo(
            WarehouseVnLadingItem::class,
            'code',
            'lading_code'
        );
    }

    public function billCodeOut()
    {
        return $query = $this->belongsTo(
            BillCode::class,
            'bill_code',
            'bill_code'
        );
    }

    public function billCode()
    {
        return $query = $this->belongsTo(
            BillCode::class,
            'bill_code',
            'bill_code'
        );
    }

    public function verifyLadingCode()
    {
        return $query = $this->belongsTo(
            VerifyLadingCode::class,
            'code',
            'lading_code'
        );
    }
}
