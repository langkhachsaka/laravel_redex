<?php

namespace Modules\BillOfLading\Models;

use Modules\Base\Models\BaseModel;
use Modules\Complaint\Models\Complaint;
use Modules\CourierCompany\Models\CourierCompany;
use Modules\Customer\Models\Customer;
use Modules\Customer\Models\CustomerAddress;
use Modules\LadingCode\Models\LadingCode;
use Modules\Notification\Models\Notification;
use Modules\Transaction\Models\Transaction;
use Modules\User\Models\User;

class BillOfLading extends BaseModel
{
    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;
    const STATUS_DELIVERY = 2;
    const STATUS_COMPLAINT = 3;
    const STATUS_FINISHED = 4;

    protected $fillable = [
        'customer_id',
        'customer_billing_name',
        'customer_billing_address',
        'customer_billing_phone',
        'customer_shipping_name',
        'customer_shipping_address',
        'customer_shipping_phone',
        'courier_company_id',
        'seller_id',
        'status',
        'end_date',
        'file_path'
    ];

    protected $appends = [
        'file_name',
        'link_view_file_online',
        'link_download_file',
        'status_name',
        'is_approved',
        'can_create_complaint'
    ];

    public function courierCompany()
    {
        return $this->belongsTo(CourierCompany::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function customerAddress()
    {
        return $this->belongsTo(CustomerAddress::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id', 'id');
    }

    public function complaint()
    {
        return $this->morphOne(Complaint::class, 'ordertable');
    }

    public function notification()
    {
        return $this->morphMany(Notification::class, 'notificationtable');
    }

    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'transactiontable');
    }

    public function ladingCodes()
    {
        return $this->morphMany(LadingCode::class, 'ladingcodetable');
    }

    //--------------------------------------------------------------------------------------//

    public function getFileNameAttribute()
    {
        return $this->file_path ? basename($this->file_path) : null;
    }

    public function getLinkDownloadFileAttribute()
    {
        return $this->file_path ? asset('/storage/' . $this->file_path) : null;
    }

    public function getLinkViewFileOnlineAttribute()
    {
        $viewerUrl = 'https://view.officeapps.live.com/op/view.aspx?src=';

        return $viewerUrl . urlencode($this->getLinkDownloadFileAttribute());
    }

    public function getStatusNameAttribute()
    {
        $status = [
            self::STATUS_PENDING => 'Chờ duyệt',
            self::STATUS_APPROVED => 'Đã duyệt',
            self::STATUS_DELIVERY => 'Đang giao hàng',
            self::STATUS_COMPLAINT => 'Khiếu nại',
            self::STATUS_FINISHED => 'Hoàn thành',
        ];
        return data_get($status, $this->status);
    }

    public function getIsApprovedAttribute()
    {
        return $this->status != self::STATUS_PENDING;
    }

    public function getCanCreateComplaintAttribute()
    {
        return true;
    }
}
