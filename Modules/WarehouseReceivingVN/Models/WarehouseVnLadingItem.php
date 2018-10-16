<?php
/**
 * Created by PhpStorm.
 * User: CongHD
 * Date: 16/05/2018
 * Time: 2:28 CH
 */

namespace Modules\WarehouseReceivingVN\Models;


use Modules\Base\Models\BaseModel;
use Modules\LadingCode\Models\LadingCode;
use Modules\Shipment\Models\Shipment;
use Modules\VerifyLadingCode\Models\SubLadingCode;
use Modules\VerifyLadingCode\Models\VerifyLadingCode;
use Modules\Warehouse\Models\Warehouse;
use Modules\User\Models\User;

class WarehouseVnLadingItem extends BaseModel
{
    const STATUS_TEMPORARY  = 1;
    const STATUS_SUBMITED = 2;
    const STATUS_WAIT_TEST = 3;
    const STATUS_CHECKED = 4;
    const STATUS_ERROR = 5;
    const STATUS_PROCESS_PAYMENT = 6;
    const STATUS_PAYMENTED = 7;
    const STATUS_WAIT_TRANFER = 8;
    const STATUS_TRANFERED = 9;
    const STATUS_RECEIVED = 10;

    const DISABLED = 99;
    const UNPACK = 0;

    protected $fillable = [
        'warehouse_receiving_v_ns_id',
        'lading_code',
        'sub_lading_code',
        'weight',
        'height',
        'width',
        'length',
        'pack',
        'other_fee',
        'status',
        'customer_order_id',
    ];

    protected $appends = [
        'status_name',
        'pack_name',
    ];

    public function warehouseReceivingVN()
    {
        return $this->belongsTo(WarehouseReceivingVN::class,'warehouse_receiving_v_ns_id', 'id');
    }

    public function ladingCodes()
    {
        return $this->hasMany(LadingCode::class,'code', 'lading_code');
    }
    public function verifyLadingCode()
    {
        return $this->belongsTo(VerifyLadingCode::class,'lading_code', 'lading_code');
    }

    public function subLadingCode()
    {
        return $this->belongsTo(SubLadingCode::class,'sub_lading_code', 'sub_lading_code');
    }

    public function haveSubLadingCode()
    {
        return $this->hasMany(SubLadingCode::class,'lading_code', 'lading_code');
    }

    public function getStatusNameAttribute()
    {
        $status = [
            self::STATUS_TEMPORARY => 'Lưu tạm',
            self::STATUS_SUBMITED => 'Đã xác nhận',
            self::STATUS_WAIT_TEST => 'Chờ kiểm',
            self::STATUS_CHECKED => 'Đã kiểm',
            self::STATUS_ERROR => 'Kiện hàng lỗi',
            self::STATUS_PROCESS_PAYMENT => 'Xử lý thanh toán',
            self::STATUS_PAYMENTED => 'Đã thanh toán',
            self::STATUS_WAIT_TRANFER => 'Chờ phát',
            self::STATUS_TRANFERED => 'Đã phát',
            self::STATUS_RECEIVED => 'Đã nhận',
        ];
        return data_get($status, $this->status);
    }

    public function getPackNameAttribute()
    {
        $packes = [
            0 => 'Không',
            1 => 'Đóng gỗ',
            2 => 'Nẹp bìa',
        ];
        return data_get($packes, $this->pack);
    }

}
