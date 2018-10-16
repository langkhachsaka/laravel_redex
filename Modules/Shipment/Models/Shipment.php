<?php

namespace Modules\Shipment\Models;

use Modules\Base\Models\BaseModel;
use Modules\User\Models\User;
use Modules\Warehouse\Models\Warehouse;
use Modules\WarehouseReceivingVN\Models\WarehouseReceivingVN;

class Shipment extends BaseModel
{
    const NEW_BILL_OF_LADING_CODE_PREFIX = 'NEW_';
    const DELETE_BILL_OF_LADING_CODE_PREFIX = 'DEL_';
    const PREFIX_SHIPMENT_CODE = 'BT';
    const NEW_SHIPMENT_CODE = 'BT00001';
    const NUM_CHAR_INT = 5;
    const FAST_TRANSPORT = 1;
    const SLOW_TRANSPORT = 2;
    const NOT_DEFINE_TRANSPORT = 999;
    const STATUS_NEW = 1;
    const STATUS_DONE = 2;
    const STATUS_RECIEVED_MATCH = 3;
    const STATUS_RECIEVED_UNMATCH = 4;
    protected $fillable = [
        'shipment_code',
        'status',
        'real_weight',
        'creator_id',
        'transport_date',
        'receive_date',
        'warehouse_id',
        'volume',
        'transport_type',
        'conversion_factor',
        'note',
    ];
    protected $appends = [
        'status_name',
        'transport_type_name',
        'conversion_weight'
    ];

    public function userCreator()
    {
        return $this->belongsTo(User::class,'creator_id', 'id')->select('id','name');
    }
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class,'warehouse_id', 'id')->select('id','name');
    }

    public function shipmentItem()
    {
        return $this->hasMany(ShipmentItem::class,'shipment_code', 'shipment_code');
    }

    public function warehouseVn()
    {
        return $this->belongsTo(WarehouseReceivingVN::class,'shipment_code', 'shipment_code');
    }

    public function getTransportTypeNameAttribute()
    {
        $transport_type = [
            self::FAST_TRANSPORT => 'Nhanh',
            self::SLOW_TRANSPORT => 'Thường',
        ];
        return data_get($transport_type, $this->transport_type);
    }
    public function getStatusNameAttribute()
    {
        $status = [
            self::STATUS_NEW => 'Lô hàng mới',
            self::STATUS_DONE => 'Đã vận chuyển',
            self::STATUS_RECIEVED_MATCH => 'Đã nhận',
            self::STATUS_RECIEVED_UNMATCH => 'Đã nhận - Không khớp hàng',
        ];
        return data_get($status, $this->status);
    }
    public function getConversionWeightAttribute()
    {
        if($this->conversion_factor &&  $this->length && $this->width && $this->height) {
            $conversionWeight = ($this->length * $this->width * $this->height)/$this->conversion_factor;
            if($conversionWeight > $this->real_weight){
                return $conversionWeight;
            } else {
                return $this->real_weight;
            }
        } else {
            return null;
        }

    }
}
