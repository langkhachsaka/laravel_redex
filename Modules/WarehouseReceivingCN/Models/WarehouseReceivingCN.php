<?php
/**
 * Created by PhpStorm.
 * User: NguyenTri
 * Date: 03/05/2018
 * Time: 2:28 CH
 */

namespace Modules\WarehouseReceivingCN\Models;


use Modules\Base\Models\BaseModel;
use Modules\LadingCode\Models\LadingCode;
use Modules\Shipment\Models\ShipmentItem;
use Modules\Warehouse\Models\Warehouse;
use Modules\User\Models\User;

class WarehouseReceivingCN extends BaseModel
{
    const STATUS_MATCHED  = 1;
    const STATUS_UNMATCHED = 2;

    protected $fillable = [
        'warehouse_id',
        'date_receiving',
        'weight',
        'height',
        'width',
        'length',
        'note',
        'bill_of_lading_code',
        'user_receive_id',
        'status'
    ];

    protected $appends = [
        'status_name'
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class,'warehouse_id', 'id');
    }

    public function userReceive()
    {
        return $this->belongsTo(User::class,'user_receive_id', 'id');
    }

    public function shipmentItem()
    {
        return $this->belongsTo(ShipmentItem::class,'bill_of_lading_code', 'bill_of_lading_code');
    }
    public function ladingCode()
    {
        return $this->hasMany(LadingCode::class,'code', 'bill_of_lading_code');
    }


    public function getStatusNameAttribute()
    {
        $status = [
            self::STATUS_MATCHED => 'Khớp',
            self::STATUS_UNMATCHED => 'Chưa khớp',
        ];
        return data_get($status, $this->status);
    }
}
