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
use Modules\Transaction\Models\PaymentInformation;
use Modules\User\Models\User;
use Modules\VerifyLadingCode\Models\VerifyLadingCode;

class DeliveryTask extends Task
{
    /**
     * Call it when created new complaint by user ( in back-end)
     * @param Complaint $complaint
     */
    public static function newTaskDelivery($transactionDetail,$performer_id)
    {
        try{
            DB::beginTransaction();
            $task = new Task();
            $task->title = 'Giao hàng cho khách hàng : '.$transactionDetail['customer']['name'];
            $task->status = self::DELIVERY_PENDING;
            $task->task_type = self::TYPE_DELIVERY;
            $task->creator_id = auth()->user()->id;
            $task->start_date = date('Y/m/d h:i:s');
            $task->performer_id = $performer_id;
            $task->transaction_id = $transactionDetail['id'];

            $description = 'Xuất hàng cho : <br/>';
            foreach ($transactionDetail['payment_info'] as $payment_info){
                $dataJson = json_decode($payment_info['data']);
                if($payment_info['type'] == PaymentInformation::TYPE_ADDRESS){
                    $address = $dataJson->address;
                    $ladingCodes = implode(' ',$dataJson->lading_code);
                    $description = $description. 'Địa chỉ : <b>'.$address.'</b> : Mã vận đơn <b>'.$ladingCodes . '</b><br/>';
                }
            }
            $task->description = $description;
            $task->save();
            $updater_id = auth()->user()->id;
            $comment = 'Nhiệm vụ xuất hàng được tạo bởi người dùng '.auth()->user()->name;
            TasksUpdate::copyTask($task, $updater_id, $comment,$transactionDetail['id']);


            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    public static function confirmDelivery($transactionId)
    {
        try{
            DB::beginTransaction();

            $task = Task::where('transaction_id', $transactionId)->first();
            $task->status = self::DELIVERY_PROCESSED;
            $task->save();

            $updater_id = auth()->user()->id;
            $comment = 'Đã giao hàng xong';
            TasksUpdate::copyTask($task, $updater_id, $comment);
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }


}
