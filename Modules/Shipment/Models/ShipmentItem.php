<?php

namespace Modules\Shipment\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\LadingCode\Models\LadingCode;
use Modules\WarehouseReceivingCN\Models\WarehouseReceivingCN;
use Modules\WarehouseReceivingVN\Models\WarehouseVnLadingItem;

class ShipmentItem extends Model
{

    protected $fillable = [
	'shipment_code',
	'bill_of_lading_code',
    ];

    public function billOfLading()
    {
        return $this->belongsTo(WarehouseReceivingCN::class,'bill_of_lading_code', 'bill_of_lading_code');
    }
    public function shipment()
    {
        return $this->belongsTo(Shipment::class,'shipment_code', 'shipment_code');
    }

    public function ladingCodes()
    {
        return $this->hasMany(LadingCode::class,'code', 'bill_of_lading_code');
    }

    public function warehouseVnLadingItem()
    {
        return $this->belongsTo(WarehouseVnLadingItem::class,'bill_of_lading_code', 'lading_code');
    }



}
