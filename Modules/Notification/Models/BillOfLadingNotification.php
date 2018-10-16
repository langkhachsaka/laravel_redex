<?php
/**
 * Created by PhpStorm.
 * User: NguyenTri
 * Date: 15/05/2018
 * Time: 3:21 CH
 */

namespace Modules\Notification\Models;

use Modules\BillOfLading\Models\BillOfLading;
use Modules\User\Models\User;

class BillOfLadingNotification extends Notification
{
    /**
     * Call it when created new customer order
     * It will create new notification for admin and customer service management
     * @param int $billOfLadingID
     * @param int $fromUserID
     */
    public static function newBillOffLadingByUser($billOfLadingID, $fromUserID)
    {
        $fromUser = User::findOrFail($fromUserID);

        foreach (User::where('id', '!=', $fromUserID)
                     ->where(function ($query) {
                         $query->where('role', '=', User::ROLE_ADMIN)
                             ->orWhere('role', '=', User::ROLE_CUSTOMER_SERVICE_MANAGEMENT);
                     })->get() as $user) {
            $notification = self::baseNotification(BillOfLading::class, $billOfLadingID, $fromUserID, $user->id);

            $notification->content = $fromUser->name . ' đã tạo đơn vận chuyển #' . $billOfLadingID;

            $notification->save();
        }
    }

    public static function newBillOffLadingByCustomer($billOfLadingID, $fromCustomerID)
    {
        $fromCustomer = User::findOrFail($fromCustomerID);

        foreach (User::where('role', '=', User::ROLE_ADMIN)
                     ->orWhere('role', '=', User::ROLE_CUSTOMER_SERVICE_MANAGEMENT)
                     ->get() as $user) {
            $notification = self::baseNotification(BillOfLading::class, $billOfLadingID, null, $user->id);

            $notification->content = $fromCustomer->name . ' đã tạo đơn vận chuyển #' . $billOfLadingID;

            $notification->save();
        }
    }

    /**
     * Call it when management assigned a bill of lading to customer service officer
     * @param int $billOfLadingID
     * @param int $toOfficerID
     * @param int $fromManagementID
     */
    public static function assignBillOfLading($billOfLadingID, $toOfficerID, $fromManagementID)
    {
        $management = User::findOrFail($fromManagementID);
        $officer = User::findOrFail($toOfficerID);

        foreach (User::where('id', '!=', $fromManagementID)
                     ->where(function ($query) use ($toOfficerID) {
                         $query->where('role', '=', User::ROLE_ADMIN)
                             ->orWhere('role', '=', User::ROLE_CUSTOMER_SERVICE_MANAGEMENT)
                             ->orWhere('id', '=', $toOfficerID);
                     })->get() as $user) {
            $notification = self::baseNotification(BillOfLading::class, $billOfLadingID, $fromManagementID, $user->id);

            if ($user->id == $toOfficerID) {
                $notification->content = 'Đơn vận chuyển #' . $billOfLadingID . ' được phân công cho bạn';
            } else {
                $notification->content = $management->name . ' đã phân công đơn vận chuyển #' . $billOfLadingID . ' cho ' . $officer->name;
            }

            $notification->save();
        }
    }

    public static function unAssignBillOfLading($billOfLadingID, $toOfficerID, $fromManagementID)
    {
        $management = User::findOrFail($fromManagementID);

        $notification = self::baseNotification(BillOfLading::class, $billOfLadingID, $fromManagementID, $toOfficerID);

        $notification->content = $management->name . ' đã phân công đơn vận chuyển #' . $billOfLadingID . ' cho người khác';

        $notification->save();
    }

    /**
     * Call it when management approved order
     * It will create new notification for admin and order's seller
     * @param int $billOfLadingID
     * @param int $toOfficerID
     * @param int $fromManagementID
     */
    public static function approveBillOfLading($billOfLadingID, $toOfficerID, $fromManagementID)
    {
        $management = User::findOrFail($fromManagementID);

        foreach (User::where('id', '!=', $fromManagementID)
                     ->where(function ($query) use ($toOfficerID) {
                         $query->where('role', '=', User::ROLE_ADMIN)
                             ->orWhere('role', '=', User::ROLE_CUSTOMER_SERVICE_MANAGEMENT)
                             ->orWhere('role', '=', User::ROLE_ACCOUNTANT)
                             ->orWhere('id', '=', $toOfficerID);
                     })->get() as $user) {
            $notification = self::baseNotification(BillOfLading::class, $billOfLadingID, $fromManagementID, $user->id);

            if ($user->isAccountant()) {
                $notification->content = 'Yêu cầu xử lý đặt cọc đơn vận chuyển #' . $billOfLadingID;
            } else {
                $notification->content = $management->name . ' đã duyệt đơn vận chuyển #' . $billOfLadingID;
            }

            $notification->save();
        }
    }

    /**
     * @param int $orderID
     * @param int $toOfficerID
     * @param int $fromManagementID
     */
    public static function deleteBillOfLadingByManagement($orderID, $toOfficerID, $fromManagementID)
    {
        $management = User::findOrFail($fromManagementID);

        foreach (User::where('id', '!=', $fromManagementID)
                     ->where(function ($query) use ($toOfficerID) {
                         $query->where('role', '=', User::ROLE_ADMIN)
                             ->orWhere('role', '=', User::ROLE_CUSTOMER_SERVICE_MANAGEMENT)
                             ->orWhere('id', '=', $toOfficerID);
                     })->get() as $user) {
            $notification = self::baseNotification(BillOfLading::class, $orderID, $fromManagementID, $user->id);

            $notification->content = $management->name . ' đã xóa đơn vận chuyển #' . $orderID;

            $notification->save();
        }
    }

    /**
     * @param int $orderID
     * @param int $toOfficerID
     * @param int $fromCustomerID
     */
    public static function deleteBillOfLadingByCustomer($orderID, $toOfficerID, $fromCustomerID)
    {
        $customer = User::findOrFail($fromCustomerID);

        foreach (User::where('role', '=', User::ROLE_ADMIN)
                     ->orWhere('role', '=', User::ROLE_CUSTOMER_SERVICE_MANAGEMENT)
                     ->orWhere('id', '=', $toOfficerID)
                     ->get() as $user) {
            $notification = self::baseNotification(BillOfLading::class, $orderID, null, $user->id);

            $notification->content = 'Khách hàng ' . $customer->name . ' đã xóa đơn vận chuyển #' . $orderID;

            $notification->save();
        }
    }
}