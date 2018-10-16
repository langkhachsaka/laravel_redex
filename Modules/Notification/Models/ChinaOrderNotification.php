<?php
/**
 * Created by PhpStorm.
 * User: NguyenTri
 * Date: 15/05/2018
 * Time: 3:22 CH
 */

namespace Modules\Notification\Models;

use Modules\ChinaOrder\Models\ChinaOrder;
use Modules\User\Models\User;

class ChinaOrderNotification extends Notification
{
    /**
     * @param int $orderID
     * @param int $fromUserID
     */
    public static function newChinaOrder($orderID, $fromUserID)
    {
        $fromUser = User::findOrFail($fromUserID);

        foreach (User::where('id', '!=', $fromUserID)
                     ->where(function ($query) {
                         $query->where('role', '=', User::ROLE_ADMIN)
                             ->orWhere('role', '=', User::ROLE_ORDERING_MANAGEMENT);
                     })->get() as $user) {
            $notification = self::baseNotification(ChinaOrder::class, $orderID, $fromUserID, $user->id);

            $notification->content = $fromUser->name . ' đã tạo đơn hàng TQ #' . $orderID;

            $notification->save();
        }
    }

    /**
     * @param int $orderID
     * @param int $toOfficerID
     * @param int $fromManagementID
     */
    public static function assignChinaOrder($orderID, $toOfficerID, $fromManagementID)
    {
        $management = User::findOrFail($fromManagementID);
        $officer = User::findOrFail($toOfficerID);

        foreach (User::where('id', '!=', $fromManagementID)
                     ->where(function ($query) use ($toOfficerID) {
                         $query->where('role', '=', User::ROLE_ADMIN)
                             ->orWhere('role', '=', User::ROLE_ORDERING_MANAGEMENT)
                             ->orWhere('id', '=', $toOfficerID);
                     })->get() as $user) {
            $notification = self::baseNotification(ChinaOrder::class, $orderID, $fromManagementID, $user->id);

            if ($user->id == $toOfficerID) {
                $notification->content = $management->name . ' đã phân công đơn hàng TQ #' . $orderID . ' cho bạn';
            } else {
                $notification->content = $management->name . ' đã phân công đơn hàng TQ #' . $orderID . ' cho ' . $officer->name;
            }

            $notification->save();
        }
    }

    /**
     * @param int $orderID
     * @param int $toOfficerID
     * @param int $fromManagementID
     */
    public static function unAssignChinaOrder($orderID, $toOfficerID, $fromManagementID)
    {
        $management = User::findOrFail($fromManagementID);

        $notification = self::baseNotification(ChinaOrder::class, $orderID, $fromManagementID, $toOfficerID);

        $notification->content = $management->name . ' đã phân công đơn hàng TQ #' . $orderID . ' cho người khác';

        $notification->save();
    }


    /**
     * @param int $orderID
     * @param int $toOfficerID
     * @param int $fromManagementID
     */
    public static function approveChinaOrder($orderID, $toOfficerID, $fromManagementID)
    {
        $management = User::findOrFail($fromManagementID);

        foreach (User::where('id', '!=', $fromManagementID)
                     ->where(function ($query) use ($toOfficerID) {
                         $query->where('role', '=', User::ROLE_ADMIN)
                             ->orWhere('role', '=', User::ROLE_ORDERING_MANAGEMENT)
                             ->orWhere('role', '=', User::ROLE_ACCOUNTANT)
                             ->orWhere('id', '=', $toOfficerID);
                     })->get() as $user) {
            $notification = self::baseNotification(ChinaOrder::class, $orderID, $fromManagementID, $user->id);

            if ($user->isAccountant()) {
                $notification->content = 'Yêu cầu xử lý thanh toán đơn hàng TQ #' . $orderID;
            } else {
                $notification->content = $management->name . ' đã duyệt đơn hàng TQ #' . $orderID;
            }

            $notification->save();
        }
    }

    /**
     * @param int $orderID
     * @param int $toOfficerID
     * @param int $fromManagementID
     */
    public static function deleteChinaOrder($orderID, $toOfficerID, $fromManagementID)
    {
        $management = User::findOrFail($fromManagementID);

        foreach (User::where('id', '!=', $fromManagementID)
                     ->where(function ($query) use ($toOfficerID) {
                         $query->where('role', '=', User::ROLE_ADMIN)
                             ->orWhere('role', '=', User::ROLE_ORDERING_MANAGEMENT)
                             ->orWhere('id', '=', $toOfficerID);
                     })->get() as $user) {
            $notification = self::baseNotification(ChinaOrder::class, $orderID, $fromManagementID, $user->id);

            $notification->content = $management->username . ' đã xóa đơn hàng TQ #' . $orderID;

            $notification->save();
        }
    }
}
