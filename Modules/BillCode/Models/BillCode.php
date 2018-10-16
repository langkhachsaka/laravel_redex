<?php
/**
 * Created by PhpStorm.
 * User: NguyenTri
 * Date: 26/06/2018
 * Time: 8:54 SA
 */
namespace Modules\BillCode\Models;

use Modules\Base\Models\BaseModel;
use Modules\CustomerOrder\Models\CustomerOrder;
use Modules\CustomerOrder\Models\CustomerOrderItem;
use Modules\LadingCode\Models\LadingCode;
use Modules\Shop\Models\Shop;

class BillCode extends BaseModel
{
    const CONST_1 = 1;
    const CONST_2 = 2;
    protected $fillable = [
        'bill_code',
        'shop_id',
        'customer_order_id',
        'order_date',
        'delivery_type',
        'insurance_type',
        'reinforced_type',
        'fee_ship_inland',
    ];


    protected static function boot()
    {
        parent::boot();

        // Auto update bill_code value in `lading_codes` table when bill_code in `bill_codes` is changed
        self::updated(function ($model) {
            /** @var self $model */
            if ($model->isDirty('bill_code') && $model->getOriginal('bill_code')) {
                LadingCode::query()
                    ->where('bill_code', $model->getOriginal('bill_code'))
                    ->update(['bill_code' => $model->bill_code]);
            }
        });
    }


    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function customerOrder()
    {
        return $this->belongsTo(CustomerOrder::class);
    }

    public function customerOrderItems()
    {
        $query = $this->hasMany(
            CustomerOrderItem::class,
            'customer_order_id',
            'customer_order_id'
        )
            ->where('shop_id','=',$this->shop_id);
        return $query;
    }

    public function ladingCodes()
    {
        return $this->hasMany(LadingCode::class, 'bill_code', 'bill_code');
    }

    public function getDeliveryTypeNameAttribute(){
        $delivery_type = [
            self::CONST_1 => 'thường',
            self::CONST_2 => 'nhanh'

        ];
        return data_get($delivery_type, $this->delivery_type);
    }

    public function getInsuranceTypeNameAttribute(){
        $delivery_type = [
            self::CONST_1 => 'không',
            self::CONST_2 => 'có'
        ];
        return data_get($delivery_type, $this->insurance_type);
    }

    public function getReinforcedTypeNameAttribute(){
        $delivery_type = [
            self::CONST_1 => 'đóng gỗ',
            self::CONST_2 => 'bìa cát tông'

        ];
        return data_get($delivery_type, $this->reinforced_type);
    }
}