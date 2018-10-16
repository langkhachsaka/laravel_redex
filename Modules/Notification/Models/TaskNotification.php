<?php
/**
 * Created by PhpStorm.
 * User: NguyenTri
 * Date: 16/05/2018
 * Time: 9:21 SA
 */

namespace Modules\Notification\Models;

use Modules\User\Models\User;
use Modules\Task\Models\Task;

class TaskNotification extends Notification
{

    /**
     * @param int $taskID
     * @param int $creatorID
     */
    public static function newTask($taskID, $creatorID = null)
    {
        foreach (User::where('id', '!=', $creatorID)
                     ->where('role', '=', User::ROLE_ADMIN)
                     ->get() as $user) {
            $notification = self::baseNotification(Task::class, $taskID, $creatorID, $user->id);

            if (empty($creatorID)) {
                $notification->content = 'Task #' . $taskID . ' đã được tạo tự động';
            } else {
                $creator = User::findOrFail($creatorID);
                $notification->content = $creator->name . ' đã tạo task #' . $taskID;
            }

            $notification->save();
        }
    }

    /**
     * @param int $taskID
     * @param int $toPerformerID
     * @param int $fromManagementID
     */
    public static function assignTask($taskID, $toPerformerID, $fromManagementID)
    {
        $notification = self::baseNotification(Task::class, $taskID, $fromManagementID, $toPerformerID);

        $creator = User::findOrFail($fromManagementID);
        $notification->content = $creator->name . ' đã phân công task #' . $taskID . ' cho bạn';

        $notification->save();
    }

}
