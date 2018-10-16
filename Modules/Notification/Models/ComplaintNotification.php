<?php
/**
 * Created by PhpStorm.
 * User: NguyenTri
 * Date: 16/05/2018
 * Time: 3:26 CH
 */

namespace Modules\Notification\Models;

use Modules\Customer\Models\Customer;
use Modules\CustomerOrder\Models\CustomerOrder;
use Modules\User\Models\User;
use Modules\Complaint\Models\Complaint;

class ComplaintNotification extends Notification
{
    /**
     * @param int $complaintID
     * @param int $orderID
     * @param int $fromUser
     * @param string $orderType
     */
    public static function newComplaintByUser($complaintID, $orderID, $orderType, $fromUser)
    {
        $strType = $orderType === CustomerOrder::class ? 'vận chuyển ' : '';

        foreach (User::where('id', '!=', $fromUser)
                     ->where(function ($query) {
                         $query->where('role', '=', User::ROLE_ADMIN)
                             ->orWhere('role', '=', User::ROLE_CUSTOMER_SERVICE_MANAGEMENT);
                     })->get() as $user) {
            $notification = Notification::baseNotification(Complaint::class, $complaintID, $fromUser, $user->id);

            $notification->content = 'Khiếu nại #' . $complaintID . ' được tạo cho đơn hàng ' . $strType . '#' . $orderID;

            $notification->save();
        }
    }

    /**
     * @param int $complaintID
     * @param int $orderID
     * @param int $toUser
     * @param int $fromUser
     */
    public static function newComplaintByCustomer($complaintID, $orderID, $orderType, $toUser, $fromUser)
    {
        $strType = $orderType === CustomerOrder::class ? 'vận chuyển ' : '';

        foreach (User::where('role', '=', User::ROLE_ADMIN)
                     ->orWhere('role', '=', User::ROLE_CUSTOMER_SERVICE_MANAGEMENT)
                     ->orWhere('id', '=', $toUser)
                     ->get() as $user) {
            $notification = Notification::baseNotification(Complaint::class, $complaintID, $fromUser, $user->id);

            $notification->content = 'Khách hàng đã tạo khiếu nại #' . $complaintID . ' cho đơn hàng ' . $strType . '#' . $orderID;

            $notification->save();
        }
    }

    /**
     * @param int $complaintID
     * @param int $orderID
     * @param int $toUser
     * @param string $orderType
     */
    public static function assignComplaint($complaintID, $orderID, $orderType, $toUser)
    {
        $officer = User::findOrFail($toUser);
        $strType = $orderType === CustomerOrder::class ? 'vận chuyển ' : '';

        foreach (User::where('role', '=', User::ROLE_ADMIN)
                     ->orWhere('role', '=', User::ROLE_CUSTOMER_SERVICE_MANAGEMENT)
                     ->orWhere('id', '=', $toUser)
                     ->get() as $user) {
            $notification = Notification::baseNotification(Complaint::class, $complaintID, null, $user->id);

            if ($user->id == $toUser) {
                $notification->content = 'Bạn đã được giao xử lý khiếu nại  #' . $complaintID . ' cho đơn hàng ' . $strType . '#' . $orderID;
            } else {
                $notification->content = $officer->name . ' được giao xử lý khiếu nại #' . $complaintID . ' cho đơn hàng ' . $strType . '#' . $orderID;
            }
            $notification->save();
        }
    }

    /**
     * @param int $complaintID
     * @param int $orderID
     * @param int $toUserID
     * @param int $fromCustomerID
     */
    public static function deleteComplaintByCustomer($complaintID, $orderID, $orderType, $toUserID, $fromCustomerID)
    {
        $customer = Customer::findOrFail($fromCustomerID);
        $strType = $orderType === CustomerOrder::class ? 'vận chuyển ' : '';

        foreach (User::where('role', '=', User::ROLE_ADMIN)
                     ->orWhere('role', '=', User::ROLE_CUSTOMER_SERVICE_MANAGEMENT)
                     ->orWhere('id', '=', $toUserID)
                     ->get() as $user) {
            $notification = Notification::baseNotification(Complaint::class, $complaintID, null, $user->id);

            $notification->content = $customer->name . ' đã xóa khiếu nại #' . $complaintID . ' của đơn hàng ' . $strType . '#' . $orderID;

            $notification->save();
        }
    }

    /**
     * @param int $complaintID
     * @param int $orderID
     * @param int $toUserID
     * @param int $fromManagementID
     */
    public static function deleteComplaintByManagement($complaintID, $orderID, $orderType, $toUserID, $fromManagementID)
    {
        $management = User::findOrFail($fromManagementID);
        $strType = $orderType === CustomerOrder::class ? 'vận chuyển ' : '';

        foreach (User::where('id', '!=', $fromManagementID)
                     ->where(function ($query) use ($toUserID) {
                         $query->where('role', '=', User::ROLE_ADMIN)
                             ->orWhere('role', '=', User::ROLE_CUSTOMER_SERVICE_MANAGEMENT)
                             ->orWhere('id', '=', $toUserID);
                     })->get() as $user) {
            $notification = Notification::baseNotification(Complaint::class, $complaintID, null, $user->id);

            $notification->content = $management->name . ' đã xóa khiếu nại #' . $complaintID . ' của đơn hàng ' . $strType . '#' . $orderID;

            $notification->save();
        }
    }
}
