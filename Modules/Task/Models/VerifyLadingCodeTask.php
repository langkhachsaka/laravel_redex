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

class VerifyLadingCodeTask extends Task
{
    /**
     * Call it when created new complaint by user ( in back-end)
     * @param Complaint $complaint
     */
    public static function newTaskVerifyLadingCode($ladingCodes,$performer_id)
    {
        try{
            DB::beginTransaction();

                $task = new Task();
                $task->title = 'Kiểm hàng cho những kiện hàng :'.$ladingCodes;
                $task->status = self::VERIFY_PENDING;
                $task->task_type = self::TYPE_VERIFY_LADING_CODE;
                $task->creator_id = auth()->user()->id;
                $task->start_date = date('Y/m/d h:i:s');
                $task->performer_id = $performer_id;
                $task->lading_codes = $ladingCodes;
                $task->description = 'Kiểm hàng cho những kiện hàng :<b><i>'.$ladingCodes.' </i></b>';
                $task->save();
                $updater_id = auth()->user()->id;
                $comment = 'Nhiệm vụ kiểm hàng được tạo bởi người dùng '.auth()->user()->name;
                TasksUpdate::copyTask($task, $updater_id, $comment);


            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    public static function updateVerifyTask($ladingCode)
    {
        try{
            DB::beginTransaction();

            $task = Task::WhereFullLike('lading_codes', $ladingCode)->first();
            $ladingCodes = $task->lading_codes;
            $listladingCode = explode(' ',$ladingCodes);

            if(count($listladingCode) == 1){
                $task->status = self::VERIFY_PROCESSED;
                $task->end_date = date('Y/m/d h:i:s');
                $task->performer_id = auth()->user()->id;
            } else {
                $processedALl = true;
                $verifyLadingCodes = VerifyLadingCode::whereIn('lading_code',$listladingCode)->select('status')->get();
                foreach ($verifyLadingCodes as $key=>$verifyLadingCode){
                    if($verifyLadingCode->status == VerifyLadingCode::NOT_YET_VERIFY ){
                       $processedALl = false;
                       break;
                    }
                }
                if($processedALl){
                    $task->status = self::VERIFY_PROCESSED;
                    $task->end_date = date('Y/m/d h:i:s');
                    $task->performer_id = auth()->user()->id;
                } else {
                    $task->status = self::VERIFY_PROCESSING;
                    $task->performer_id = auth()->user()->id;
                }
            }
            $task->save();

            $updater_id = auth()->user()->id;
            $comment = 'Đã kiểm mã vận đơn : '.$ladingCode;
            TasksUpdate::copyTask($task, $updater_id, $comment);


            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }


}
