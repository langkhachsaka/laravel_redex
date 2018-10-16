<?php
/**
 * Created by PhpStorm.
 * User: CongHD
 * Date: 16/05/2018
 * Time: 2:28 CH
 */

namespace Modules\WarehouseReceivingVN\Models;


use Modules\Base\Models\BaseModel;
use Modules\Shipment\Models\Shipment;
use Modules\Warehouse\Models\Warehouse;
use Modules\User\Models\User;

class WarehouseReceivingVN extends BaseModel
{
    const STATUS_NOT_YET_RECEIVE  = 0;
    const STATUS_NOT_YET_CONFIRM  = 1;
    const STATUS_CONFIRMED = 2;
    const STATUS_REPORTED = 3;

    protected $fillable = [
        'warehouse_id',
        'date_receiving',
        'shipment_code',
        'weight',
        'height',
        'width',
        'length',
        'user_receive_id',
        'note',
        'status',
        'pack',
    ];

    protected $appends = [
        'status_name',
        'status_icon',
        'pack_name',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class,'warehouse_id', 'id');
    }

    public function shipment()
    {
        return $this->belongsTo(Shipment::class,'shipment_code', 'shipment_code');
    }

    public function userReceive()
    {
        return $this->belongsTo(User::class,'user_receive_id', 'id');
    }
    public function warehouseVnLadingItems()
    {
        return $this->hasMany(WarehouseVnLadingItem::class,'warehouse_receiving_v_ns_id', 'id');
    }

    public function getStatusNameAttribute()
    {
        $status = [
            self::STATUS_NOT_YET_RECEIVE => 'Chưa nhận',
            self::STATUS_NOT_YET_CONFIRM => 'Chưa xác nhận',
            self::STATUS_CONFIRMED => 'Đã xác nhận',
            self::STATUS_REPORTED => 'Đã báo cáo',

        ];
        return data_get($status, $this->status);
    }

    public function getPackNameAttribute()
    {
        $packes = [
            null => 'Không',
            0 => 'Không',
            1 => 'Đóng gỗ',
            2 => 'Nẹp bìa',
        ];
        return data_get($packes, $this->pack);
    }
    public function getStatusIconAttribute()
    {
        $status = [
            self::STATUS_NOT_YET_CONFIRM => 'la la-info-circle',
            self::STATUS_CONFIRMED => 'la la-check',
            self::STATUS_REPORTED => 'la la-warning',
        ];
        return data_get($status, $this->status);
    }
}
