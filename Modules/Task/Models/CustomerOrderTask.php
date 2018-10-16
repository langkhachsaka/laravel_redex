<?php
/**
 * Created by PhpStorm.
 * User: NguyenTri
 * Date: 15/05/2018
 * Time: 3:15 CH
 */

namespace Modules\Task\Models;

use Modules\CustomerOrder\Models\CustomerOrder;
use Illuminate\Support\Facades\DB;
use Modules\Transaction\Models\Transaction;

class CustomerOrderTask extends Task
{
    /**
     * Call it when created new customer order by user ( in back-end)
     *CustomerOrder $cusOrder
     */
    public static function newCustomerOrderByUser(CustomerOrder $cusOrder)
    {
        try{
            DB::beginTransaction();
            $task = new Task();
            $task->customer_order_id = $cusOrder->id;
            $task->task_type = Task::TYPE_CUSTOMER_SERVICE;
            $task->title = 'Xử lý đơn hàng ' . $cusOrder->id.' cho khách hàng '.$cusOrder->customer_shipping_name;
            $task->description = 'Xử lý đơn hàng cho khách hàng <i>' . $cusOrder->customer_shipping_name. '</i>.</br><b> Địa chỉ :</b> ' .$cusOrder->customer_shipping_address . '.</br><b> Số điện thoại : </b>' . $cusOrder->customer_shipping_phone . '.';
            $task->creator_id = auth()->user()->id;
            $task->performer_id = $cusOrder->seller_id;
            $task->start_date = $cusOrder->created_at;
            $task->end_date = $cusOrder->created_at;
            $task->status = Task::WAITTING_LATCHES_ORDER;
            $task->save();

            $updater_id = auth()->user()->id;
            TasksUpdate::copyTask($task, $updater_id);
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    /**
     * Call it when created new customer order by customer ( in front-end)
     * @param int $order
     */
    public static function newCustomerOrderByCustomer($order)
    {
        try{
            DB::beginTransaction();
            $task = new Task();
            $task->customer_order_id = $order['id'];
            $task->task_type = Task::TYPE_CUSTOMER_SERVICE;
            $task->title = 'Xử lý đơn hàng '.$order['id'].' cho khách hàng '.$order['customer_shipping_name'];
            $task->description = 'Xử lý đơn hàng cho khách hàng <i>'.$order['customer_shipping_name'].'</i>.</br><b> Địa chỉ :</b> '.$order['customer_shipping_address'].
                '.</br><b> Số điện thoại : </b>'.$order['customer_shipping_phone'].'.';
            $task->start_date = $order['created_at'];
            $task->end_date = $order['created_at'];
            $task->status = Task::ORDER_INIT;
            $task->save();

            $updater_id = null;
            TasksUpdate::copyTask($task,$updater_id);
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    /**
     * Call it when update customer order by user ( in back-end)
     * @param CustomerOrder $cusOrder
     * @param int $oldSellerId
     * * @param $oldCustomerShippingName
     * @param $oldCustomerShippingAddress
     * @param $oldCustomerShippingPhone
     */
    public static function updateCustomerOrderByUser(CustomerOrder $cusOrder,$oldSellerId, $oldCustomerShippingName, $oldCustomerShippingAddress, $oldCustomerShippingPhone)
    {
        try{
            DB::beginTransaction();
            if($oldSellerId != $cusOrder->seller_id || $oldCustomerShippingName != $cusOrder->customer_shipping_name ||
                $oldCustomerShippingAddress != $cusOrder->customer_shipping_address || $oldCustomerShippingPhone != $cusOrder->customer_shipping_phone){
                //Update status of task.
                $task = Task::where('customer_order_id', $cusOrder->id)
                    ->where('task_type', Task::TYPE_CUSTOMER_SERVICE)
                    ->first();
                $task->title = 'Xử lý đơn hàng ' . $cusOrder->id.' cho khách hàng '.$cusOrder->customer_shipping_name;
                $task->description = 'Xử lý đơn hàng cho khách hàng <i>' . $cusOrder->customer_shipping_name. '</i>.</br><b> Địa chỉ :</b> ' .$cusOrder->customer_shipping_address . '.</br><b> Số điện thoại : </b>' . $cusOrder->customer_shipping_phone . '.';
                $task->performer_id = $cusOrder->seller_id;
                //If not yet assign for any one. First assign -> change status to WAITTING_LATCHES_ORDER
                if (!$oldSellerId) {
                    $task->status = Task::WAITTING_LATCHES_ORDER;
                }
                $task->save();

                $updater_id = auth()->user()->id;
                $comment = 'Đơn hàng được cập nhật bởi '.auth()->user()->name;
                TasksUpdate::copyTask($task,$updater_id, $comment);
            }
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    /**
     * Call it when update customer order by Custommer ( in front-end)
     * @param int $order
     * @param int $cusName
     */
    public static function updateCustomerOrderByCustommer($order, $cusName)
    {
        try{
            DB::beginTransaction();
            //Update status of task.
            $task = Task::where('customer_order_id', $order['id'])
                ->where('task_type', Task::TYPE_CUSTOMER_SERVICE)
                ->first();
            $task->title = 'Xử lý đơn hàng ' . $order['id'].' cho khách hàng '.$order['customer_shipping_name'];
            $task->description = 'Xử lý đơn hàng cho khách hàng <i>' . $order['customer_shipping_name']. '</i>.</br><b> Địa chỉ :</b> ' .$order['customer_shipping_address ']. '.</br><b> Số điện thoại : </b>' . $order['customer_shipping_phone'] . '.';
            $task->save();

            $updater_id = null;
            $comment = 'Đơn hàng được cập nhật bởi khách hàng '.$cusName;
            TasksUpdate::copyTask($task,$updater_id, $comment);
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    /**
     * Call it when management approved order
     * @param CustomerOrder $cusOrder
     */
    public static function approveCustomerOrder(CustomerOrder $cusOrder)
    {
        try{
            DB::beginTransaction();
            $task = Task::where('customer_order_id', $cusOrder->id)
                ->where('task_type', Task::TYPE_CUSTOMER_SERVICE)
                ->first();
            $task->status = self::APPROVED_CUSTOMER_ORDER;
            $task->save();
            $updater_id = auth()->user()->id;
            TasksUpdate::copyTask($task, $updater_id);

            // IF STATUS = APPROVE : CREATE NEW TASK FOR ACCOUNTANT
            $complaintTask = new Task();
            $complaintTask->task_type = Task::TYPE_ACCOUNTANT;
            $complaintTask->customer_order_id = $task->customer_order_id;
            $complaintTask->title = 'Xử lý đặt cọc cho đơn hàng '. $task->customer_order_id;
            $complaintTask->description = $task->description;
            $complaintTask->creator_id = auth()->user()->id;
            $complaintTask->performer_id = null;
            $complaintTask->start_date = $task->start_date;
            $complaintTask->end_date = $task->end_date;
            $complaintTask->status = Task::APPROVED_CUSTOMER_ORDER;
            $complaintTask->save();

            $updater_id = null;
            TasksUpdate::copyTask($complaintTask, $updater_id);
            //END

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }


    /**
     * Call it when begin transaction for CustormerOrder
     * @param CustomerOrder $cusOrder
     */
    public static function transactionCustomerOrder(Transaction $transaction)
    {
        try{
            DB::beginTransaction();
            $task = Task::where('customer_order_id', $transaction->transactiontable_id)
                ->where('task_type', Task::TYPE_ACCOUNTANT)
                ->first();
            $task->status = self::CUSTOMER_DEPOSITED;
            $task->performer_id = auth()->user()->id;
            $task->save();
            $updater_id = auth()->user()->id;
            $comment = 'Khách hàng đã đặt cọc ' . $transaction->money.'đ, với nội dung : '.$transaction->note.'.';
            $transactionId = $transaction->id;
            TasksUpdate::copyTask($task, $updater_id,$comment,$transactionId);

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    /**
     * Call it when management delete customer order ( in back-end)
     * @param int $cusOrderId
     */
    public static function deleteCustomerOrderByUser($cusOrderId)
    {
        try{
            DB::beginTransaction();
            //Update status of task.
            $task = Task::where('customer_order_id', $cusOrderId)
                ->where('task_type', Task::TYPE_CUSTOMER_SERVICE)
                ->first();
            $task->status = Task::ORDER_DELETED;
            $task->save();


            $updater_id = auth()->user()->id;
            $comment = 'Đã xóa bởi '.auth()->user()->name;
            TasksUpdate::copyTask($task, $updater_id, $comment);
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    /**
     * Call it when customer delete customer order ( in front-end)
     * @param int $cusOrderId
     * @param string $cusName
     */
    public static function deleteCustomerOrderByCustomer($cusOrderID, $cusName)
    {
        try{
            DB::beginTransaction();
            $task = Task::where('customer_order_id', $cusOrderID)
                ->where('task_type', Task::TYPE_CUSTOMER_SERVICE)
                ->first();
            $task->status = Task::ORDER_DELETED;
            $task->save();

            $comment = "Đã xóa bởi khách hàng ".$cusName;
            $updater_id = null;
            TasksUpdate::copyTask($task,$updater_id,$comment);
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }
}