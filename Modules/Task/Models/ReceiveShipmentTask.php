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
use Modules\VerifyLadingCode\Models\VerifyLadingCode;
use Modules\WarehouseReceivingVN\Models\WarehouseReceivingVN;

class ReceiveShipmentTask extends Task
{
    /**
     * Call it when created Task Receive Shipment in Shipment srceen
     * @param Complaint $complaint
     */
    public static function newTaskReceiveShipment($shipmentCodes,$performer_id)
    {
        try{
            DB::beginTransaction();

                $task = new Task();
                $task->title = 'Nhập hàng vào kho cho những mã lô hàng :'.$shipmentCodes;
                $task->status = self::RECEIVE_PENDING;
                $task->task_type = self::TYPE_RECEIVE_SHIPMENT;
                $task->creator_id = auth()->user()->id;
                $task->start_date = date('Y/m/d h:i:s');
                $task->performer_id = $performer_id;
                $task->shipment_codes = $shipmentCodes;
                $task->description = 'Nhập hàng vào kho cho những mã lô hàng :<b><i>'.$shipmentCodes.' </i></b>';
                $task->save();

                $updater_id = auth()->user()->id;
                TasksUpdate::copyTask($task, $updater_id);


            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    public static function updateComplaintByUser($wReceive)
    {
        try{
            DB::beginTransaction();
            $task = Task::WhereFullLike('shipment_codes', $wReceive->shipment_code)->first();

            $shipmentCodes = $task->shipment_codes;
            $listShipmentCode = explode(' ',$shipmentCodes);

            if(count($listShipmentCode) == 1){
                $task->status = self::RECEIVE_PROCESSED;
                $task->end_date = date('Y/m/d h:i:s');
                $task->performer_id = auth()->user()->id;
            } else {
                $processedALl = true;
                $verifyLadingCodes = WarehouseReceivingVN::whereIn('shipment_code',$listShipmentCode)->select('status')->get();
                foreach ($verifyLadingCodes as $key=>$verifyLadingCode){
                    if($verifyLadingCode->status != WarehouseReceivingVN::STATUS_CONFIRMED){
                       $processedALl = false;
                       break;
                    }
                }
                if($processedALl){
                    $task->status = self::RECEIVE_PROCESSED;
                    $task->end_date = date('Y/m/d h:i:s');
                    $task->performer_id = auth()->user()->id;
                } else {
                    $task->status = self::RECEIVE_PROCESSING;
                    $task->performer_id = auth()->user()->id;
                }
            }
            $task->save();

            $updater_id = auth()->user()->id;
            $comment = 'Đã nhập lô hàng : '.$wReceive->shipment_code;
            TasksUpdate::copyTask($task, $updater_id, $comment);

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }


}
