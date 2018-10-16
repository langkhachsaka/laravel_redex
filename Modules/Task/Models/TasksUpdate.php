<?php

namespace Modules\Task\Models;

use Modules\Base\Models\BaseModel;
use Modules\User\Models\User;
use Modules\Complaint\Models\Complaint;

class TasksUpdate extends BaseModel
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
    const TYPE_DELIVERING_AND_RECEIVING = 3;
    const TYPE_ACCOUNTANT = 4;
    const TYPE_VERIFY_LADING_CODE = 5;
    const TYPE_COMPLAINT = 6;


    const VERIFY_PENDING = 50;
    const VERIFY_PROCESSING = 51;
    const VERIFY_PROCESSED = 52;


    const COMPLAINT_PENDING = 60;
    const COMPLAINT_ADMIN_PROCESSED = 61;
    const COMPLAINT_CUSTOMER_SERVICE_PROCESSED = 62;
    const COMPLAINT_ORDERING_OFFICER_PROCESSED = 63;

    protected $fillable = [
        'task_id',
        'title',
        'customer_order_id',
        'task_type',
        'description',
        'complaint_id',
        'status',
        'creator_id',
        'performer_id',
        'updater_id',
        'start_date',
        'end_date',
        'comment',
        'transaction_id',
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
        return $this->belongsTo(User::class,'performer_id', 'id')->select('id','name');
    }

    public function userUpdater()
    {
        return $this->belongsTo(User::class,'updater_id', 'id')->select('id','name');
    }

    public function complaint()
    {
        return $this->belongsTo(Complaint::class,'complaint_id', 'id')->select('id');
    }


    public function getStatusNameAttribute()
    {

        return data_get(Task::LIST_STATUS, $this->status);
    }

    public static function copyTask(Task $task, $updater_id, $comment  = null, $transaction_id = null) {
        $task_update = new TasksUpdate();            
        $task_update->task_id = $task->id;
        $task_update->customer_order_id = $task->customer_order_id;
        $task_update->title = $task->title;
        $task_update->task_type = $task->task_type;
        $task_update->complaint_id = $task->complaint_id;
        $task_update->description = $task->description;
        $task_update->status = $task->status;
        $task_update->creator_id = $task->creator_id;
        $task_update->updater_id = $updater_id;
        $task_update->performer_id = $task->performer_id;
        $task_update->lading_codes = $task->lading_codes;
        $task_update->shipment_codes = $task->shipment_codes;
        $task_update->start_date = $task->start_date;
        $task_update->end_date = $task->end_date;
        $task_update->comment = $comment;
        $task_update->transaction_id = $transaction_id;
        $task_update->save();
    }

}
