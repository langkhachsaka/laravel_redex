<?php
/**
 * Created by PhpStorm.
 * User: NguyenTri
 * Date: 16/05/2018
 * Time: 3:26 CH
 */

namespace Modules\Task\Models;


use Modules\Complaint\Models\Complaint;
use Illuminate\Support\Facades\DB;
use Modules\CustomerOrder\Models\CustomerOrder;
use Modules\CustomerOrder\Models\CustomerOrderItem;
use Modules\User\Models\User;

class ComplaintTask extends Task
{
    /**
     * Call it when created new complaint by user ( in back-end)
     * @param Complaint $complaint
     */
    public static function newComplaintByUser(Complaint $complaint,$note)
    {
        try{
            DB::beginTransaction();
            $customer_order_item = CustomerOrderItem::find($complaint->customer_order_item_id);
            $customer_order_id = $customer_order_item->customer_order_id;
            if( Task::where('customer_order_id', $customer_order_id)
                ->where('task_type', Task::TYPE_COMPLAINT)
                ->exists()) {
                $task = Task::where('customer_order_id', $customer_order_id)
                    ->where('task_type', Task::TYPE_COMPLAINT)->first();
                $task->complaint_id = $task->complaint_id . "_" . $complaint->id;
                $task->description = $task->description. '</br> Sản phẩm : <b>'
                    .$customer_order_item->description . '</b> lỗi : ' . $note;
                $task->save();

                $updater_id = auth()->user()->id;
                $comment = 'Khiếu nại được cập nhật bởi người dùng '.auth()->user()->name.
                    ' </br><b> Nội dung : </b> </br> Sản phẩm : <b>'
                    .$customer_order_item->description . '</b> lỗi : ' . $note
                ;
                TasksUpdate::copyTask($task, $updater_id, $comment);
            } else {
                $task = new Task();
                $task->customer_order_id =  $customer_order_id;
                $task->title = 'Xử lý khiếu nại cho đơn hàng '.$customer_order_id;
                $task->status = self::mappingStatusComplaintAndTask($complaint->status);
                $task->task_type = self::TYPE_COMPLAINT;
                $adminUser = User::where('role',User::ROLE_ADMIN)->first();
                $task->creator_id = auth()->user()->id;
                $task->performer_id = $adminUser->id;
                $task->start_date = date('Y/m/d h:i:s');
//                $task->end_date = $complaint->created_at;
                $task->complaint_id = $complaint->id;
                $task->description = 'Xử lý khiếu nại cho đơn hàng '.$customer_order_id;
                $task->save();

                $updater_id = auth()->user()->id;
                $comment = 'Khiếu nại được cập nhật bởi người dùng '.auth()->user()->name.
                    ' </br><b> Nội dung : </b> </br> Sản phẩm : <b>'
                    .$customer_order_item->description . '</b> lỗi : ' . $note
                ;
                TasksUpdate::copyTask($task, $updater_id, $comment);
            }


            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    /**
     *  Call it when delete complaint by user ( in back-end)
     * @param Complaint $complaint
     */
    public static function deleteComplaintByUser(Complaint $complaint)
    {
        try{
            DB::beginTransaction();
            $task = Task::where('customer_order_id', $complaint->ordertable_id)
                ->where('task_type', Task::TYPE_CUSTOMER_SERVICE)
                ->first();
            $task->status = Task::COMPLAINT_ORDERING_OFFICER_PROCESSED;
            $task->save();

            $comment = 'Đã xóa khiếu nại bởi '.auth()->user()->name;
            $updater_id = auth()->user()->id;
            TasksUpdate::copyTask($task, $updater_id, $comment);
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    /**
     *   Call it when update complaint by user ( in back-end)
     * @param int $oldUserId
     * @param int $oldStatus
     * @param Complaint $complaint
     * @param string $oldContent
     */
    public static function updateComplaintByUser(Complaint $complaint)
    {
        try{
            DB::beginTransaction();

            $customer_order_item = CustomerOrderItem::find($complaint->customer_order_item_id);
            $customer_order_id = $customer_order_item->customer_order_id;
            $task = Task::where('customer_order_id', $customer_order_id)
                ->where('task_type', Task::TYPE_COMPLAINT)
                ->first();

            $listComplaint = $task->complaint_id;
            $listComplaints = explode('_',$listComplaint);
            if(count($listComplaints) == 1){
                $task->status = self::mappingStatusComplaintAndTask($complaint->status);
                $task->performer_id = auth()->user()->id;
                if($complaint->status == Complaint::STATUS_ORDERING_OFFICER_PROCESSED) {
                    $task->end_date = date('Y/m/d h:i:s');
                }
            } else {
                $processedALl = true;
                $complaints = Complaint::whereIn('id',$listComplaints)->select('status')->get();
                foreach ($complaints as $key=>$complaint){
                    if($key>=1 && $complaints[$key]->status != $complaints[$key-1]->status ){
                       $processedALl = false;
                        break;
                    }
                }
                if($processedALl){
                    $task->status = self::mappingStatusComplaintAndTask($complaint->status);
                    $task->performer_id = auth()->user()->id;
                    $task->end_date = date('Y/m/d h:i:s');
                }
            }

            $task->save();

            $updater_id = auth()->user()->id;
            $comment = 'Khiếu nại được cập nhật bởi người dùng '.auth()->user()->name;
            TasksUpdate::copyTask($task, $updater_id, $comment);


            DB::commit();
        } catch (\Exception $ex) {
            throw $ex;
            DB::rollBack();
        }
    }

    /**
     *  Call it when created new complaint by customer ( in front-end)
     * @param Complaint $complaint
     * @param string $cusName
     */
    public static function newComplaintByCustomer(Complaint $complaint,$cusName)
    {
        try{
            DB::beginTransaction();

            $task = Task::where('customer_order_id', $complaint->ordertable_id)
                ->where('task_type', Task::TYPE_CUSTOMER_SERVICE)
                ->first();
            $task->status = Task::COMPLAINT_PENDING;
            $task->complaint_id = $complaint->id;
            $task->performer_id = $complaint->user_id;
            $task->description = 'Xử lý khiếu nại cho đơn hàng '.$complaint->ordertable_id.'.</br><b>Khách hàng khiếu nại : </b> '. $cusName.'</br><b> Nội dung : </b>'.$complaint->content;
            $task->save();

            $updater_id = null;
            $comment = 'Khiếu nại được tạo bởi khách hàng : '.$cusName;
            TasksUpdate::copyTask($task, $updater_id, $comment);
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    /**
     * Call it when deleted complaint by customer ( in front-end)
     * @param int $orderID
     * @param string $cusName
     */
    public static function deleteComplaintByCustomer($orderID, $cusName)
    {
        try{
            DB::beginTransaction();
            $task = Task::where('customer_order_id', $orderID)
                ->where('task_type', Task::TYPE_CUSTOMER_SERVICE)
                ->first();
            $task->status = Task::COMPLAINT_ORDERING_OFFICER_PROCESSED;
            $task->save();

            $updater_id = null;
            $comment = 'Đã xóa khiếu nại '.$orderID.' bởi khách hàng '.$cusName;
            TasksUpdate::copyTask($task, $updater_id, $comment);
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }



    /**
     * Mapping STATUS of COMPLAINT and STATUS of TASK
     * @param $complaintStatus
     * @return int|null
     */
    private static function mappingStatusComplaintAndTask($complaintStatus){
        switch ($complaintStatus) {
            case Complaint::STATUS_PENDING:
                return Task::COMPLAINT_PENDING;
                break;
            case Complaint::STATUS_ADMIN_PROCESSED:
                return Task::COMPLAINT_ADMIN_PROCESSED;
                break;
            case Complaint::STATUS_CUSTOMER_SERVICE_PROCESSED:
                return Task::COMPLAINT_CUSTOMER_SERVICE_PROCESSED;
                break;
            case Complaint::STATUS_ORDERING_OFFICER_PROCESSED:
                return Task::COMPLAINT_ORDERING_OFFICER_PROCESSED;
                break;
            default:
                return null;
        }
    }
}
