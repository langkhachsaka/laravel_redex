<?php

namespace Modules\Task\Models;

use Modules\Base\Models\BaseModel;
use Modules\User\Models\User;
use Modules\Task\Models\TasksUpdate;
use Modules\Complaint\Models\Complaint;
use Modules\CustomerOrder\Models\CustomerOrder;

class Task extends BaseModel
{
    const ORDER_INIT = 1;
    const WAITTING_LATCHES_ORDER = 2;
    const PROCESS_LATCHES_ORDER = 3;
    const LATCHED_ORDER = 4;
    const APPROVED_CUSTOMER_ORDER = 5;
    const CANCLE_CUSTOMER_ORDER = 7;



    const ORDER_FINISHER = 11;
    const ORDER_DELETED = 12;
    const PROCESS_DEPOSITE = 20;
    const CUSTOMER_DEPOSITED = 21;
    const ORDERED_CN = 22;
    const DEPOSITE_CN = 23;
    const ORDERED_CN_SUCCESS = 24;

    const TYPE_CUSTOMER_SERVICE = 1;
    const TYPE_ORDERING = 2;
    const TYPE_DELIVERING_AND_RECEIVING = 7;
    const TYPE_ACCOUNTANT = 3;
    const TYPE_RECEIVE_SHIPMENT = 4;
    const TYPE_VERIFY_LADING_CODE = 5;
    const TYPE_COMPLAINT = 6;
    const TYPE_DELIVERY = 7;

    const RECEIVE_PENDING = 40;
    const RECEIVE_PROCESSING = 41;
    const RECEIVE_PROCESSED = 42;

    const VERIFY_PENDING = 50;
    const VERIFY_PROCESSING = 51;
    const VERIFY_PROCESSED = 52;


    const COMPLAINT_PENDING = 60;
    const COMPLAINT_ADMIN_PROCESSED = 61;
    const COMPLAINT_CUSTOMER_SERVICE_PROCESSED = 62;
    const COMPLAINT_ORDERING_OFFICER_PROCESSED = 63;

    const DELIVERY_PENDING = 70;
    const DELIVERY_PROCESSED = 71;

    const LIST_STATUS = [
            self::ORDER_INIT => 'Đơn hàng được khởi tạo',
            self::WAITTING_LATCHES_ORDER => 'Chờ chốt đơn hàng',
            self::PROCESS_LATCHES_ORDER => 'Xử lý chốt đơn hàng',
            self::LATCHED_ORDER => 'Đã chốt đơn hàng',
            self::APPROVED_CUSTOMER_ORDER => 'Đã duyệt',
            6 => 'Đơn hàng không được chấp nhận',
            self::CANCLE_CUSTOMER_ORDER => 'Hủy đơn hàng',
            self::ORDER_FINISHER => 'Kết thúc đơn hàng',
            self::ORDER_DELETED => 'Đã xóa đơn hàng',
            self::PROCESS_DEPOSITE => 'Xử lý đặt cọc',
            self::CUSTOMER_DEPOSITED => 'Khách hàng đã đặt cọc',
            self::ORDERED_CN => 'Đã đặt hàng',
            self::DEPOSITE_CN => 'Đã đặt cọc đơn hàng TQ',
            self::ORDERED_CN_SUCCESS => 'Đặt hàng thành công',

            self::RECEIVE_PENDING => 'Chờ nhận hàng',
            self::RECEIVE_PROCESSING =>'Đang nhận hàng',
            self::RECEIVE_PROCESSED => 'Đã nhận hàng',

            self::VERIFY_PENDING => 'Chưa kiểm hàng',
            self::VERIFY_PROCESSING =>'Đang kiểm hàng',
            self::VERIFY_PROCESSED =>'Đã kiểm hàng',

            self::COMPLAINT_PENDING => 'Khiếu nại chờ xử lý',
            self::COMPLAINT_ADMIN_PROCESSED => 'Admin đã duyêt',
            self::COMPLAINT_CUSTOMER_SERVICE_PROCESSED => 'NVCSKH đã xử lý',
            self::COMPLAINT_ORDERING_OFFICER_PROCESSED => 'NV Order đã xử lý',

            self::DELIVERY_PENDING => 'Chưa giao hàng',
            self::DELIVERY_PROCESSED => 'Đã giao hàng',
    ];

    protected $fillable = [
        'title',
        'customer_order_id',
        'task_type',
        'description',
        'status',
        'performer_id',
        'start_date',
        'end_date',
        'lading_code',
    ];

    protected $appends = [
        'status_name'
    ];

    public function userCreator()
    {
        return $this->belongsTo(User::class,'creator_id', 'id')->select('id','name');
    }

    public function userPerformer()
    {
        return $this->belongsTo(User::class,'performer_id', 'id')->select('id','name','role');
    }

    public function tasksUpdate()
    {
        return $this->hasMany(TasksUpdate::class,'task_id','id');
    }

    public function customerOrder()
    {
        return $this->belongsTo(CustomerOrder::class,'customer_order_id', 'id');
    }

    public function complaint()
    {
        return $this->belongsTo(Complaint::class,'complaint_id', 'id')->select('id');
    }

    public function notification()
    {
        return$this->morphMany(Notification::class, 'notificationtable');
    }

    public function getStatusNameAttribute()
    {
        return data_get(self::LIST_STATUS, $this->status);
    }


}
