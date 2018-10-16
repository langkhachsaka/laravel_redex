<?php
/**
 * Created by PhpStorm.
 * User: NguyenTri
 * Date: 15/05/2018
 * Time: 3:15 CH
 */

namespace Modules\Notification\Models;

use Modules\Customer\Models\Customer;
use Modules\CustomerOrder\Models\CustomerOrder;
use Modules\User\Models\User;

class CustomerOrderNotification extends Notification
{
    /**
     * Call it when created new customer order
     * It will create new notification for admin and customer service management
     * @param int $orderID
     * @param int $fromUserID
     */
    public static function newCustomerOrderByUser($orderID, $fromUserID)
    {
        $fromUser = User::findOrFail($fromUserID);
        foreach (User::where('id', '!=', $fromUserID)
                     ->where(function ($query) {
                         $query->where('role', '=', User::ROLE_CUSTOMER_SERVICE_MANAGEMENT)
                             ->orWhere('role', '=', User::ROLE_ADMIN);
                     })->get() as $user) {

            $notification = Notification::baseNotification(CustomerOrder::class, $orderID, $fromUserID, $user->id);

            $notification->content = $fromUser->name . ' đã tạo mới đơn hàng VN #' . $orderID;

            $notification->save();
        }
    }

    /**
     * @param int $orderID
     * @param int $fromCustomerID
     */
    public static function newCustomerOrderByCustomer($orderID, $fromCustomerID)
    {
        $customer = Customer::findOrFail($fromCustomerID);

        foreach (User::where('role', '=', User::ROLE_CUSTOMER_SERVICE_MANAGEMENT)
                     ->orWhere('role', '=', User::ROLE_ADMIN)
                     ->get() as $user) {

            $notification = Notification::baseNotification(CustomerOrder::class, $orderID, null, $user->id);

            $notification->content = 'Khách hàng ' . $customer->name . ' đã tạo mới đơn hàng VN #' . $orderID;

            $notification->save();
        }
    }

    /**
     * Call it when management assigned an order to customer service officer
     * @param int $orderID
     * @param int $toOfficerID
     * @param int $fromManagementID
     */
    public static function assignCustomerOrder($orderID, $toOfficerID, $fromManagementID)
    {
        $fromUser = User::findOrFail($fromManagementID);
        $toOfficer = User::findOrFail($toOfficerID);

        foreach (User::where('id', '!=', $fromManagementID)
                     ->where(function ($query) use ($toOfficerID) {
                         $query->where('role', '=', User::ROLE_ADMIN)
                             ->orWhere('role', '=', User::ROLE_CUSTOMER_SERVICE_MANAGEMENT)
                             ->orWhere('id', '=', $toOfficerID);
                     })->get() as $user) {

            $notification = self::baseNotification(CustomerOrder::class, $orderID, $fromManagementID, $user->id);

            if ($user->id == $toOfficerID) {
                $notification->content = $fromUser->name . ' đã phân công đơn hàng VN #' . $orderID . ' cho bạn';
            } else {
                $notification->content = $fromUser->name . ' đã phân công đơn hàng VN #' . $orderID . ' cho ' . $toOfficer->name;
            }

            $notification->save();
        }
    }

    /**
     * @param int $orderID
     * @param int $toOfficerID
     * @param int $fromManagementID
     */
    public static function unAssignCustomerOrder($orderID, $toOfficerID, $fromManagementID)
    {
        $management = User::findOrFail($fromManagementID);

        $notification = self::baseNotification(CustomerOrder::class, $orderID, $fromManagementID, $toOfficerID);

        $notification->content = $management->name . ' đã phân công đơn hàng VN #' . $orderID . ' cho người khác';
        $notification->save();
    }

    /**
     * Call it when management approved order
     * It will create new notification for admin and order's seller
     * @param int $orderID
     * @param int $toOfficerID
     * @param int $fromManagementID
     */
    public static function approveCustomerOrder($orderID, $toOfficerID, $fromManagementID)
    {
        $management = User::findOrFail($fromManagementID);

        /** @var User $user */
        foreach (User::where('id', '!=', $fromManagementID)
                     ->where(function ($query) use ($toOfficerID) {
                         $query->where('role', '=', User::ROLE_ADMIN)
                             ->orWhere('role', '=', User::ROLE_CUSTOMER_SERVICE_MANAGEMENT)
                             ->orWhere('role', '=', User::ROLE_ACCOUNTANT)
                             ->orWhere('id', '=', $toOfficerID);
                     })->get() as $user) {

            $notification = self::baseNotification(CustomerOrder::class, $orderID, $fromManagementID, $user->id);

            if ($user->isAccountant()) {
                $notification->content = 'Yêu cầu xử lý đặt cọc cho order #' . $orderID;
            } else {
                $notification->content = $management->name . ' đã duyệt đơn hàng VN #' . $orderID;
            }

            $notification->save();
        }
    }

    /**
     * @param int $orderID
     * @param int $toOfficerID
     * @param int $fromManagementID
     */
    public static function deleteCustomerOrderByManagement($orderID, $toOfficerID, $fromManagementID)
    {
        $management = User::findOrFail($fromManagementID);

        foreach (User::where('id', '!=', $fromManagementID)
                     ->where(function ($query) use ($toOfficerID) {
                         $query->where('role', '=', User::ROLE_ADMIN)
                             ->orWhere('role', '=', User::ROLE_CUSTOMER_SERVICE_MANAGEMENT)
                             ->orWhere('id', '=', $toOfficerID);
                     })->get() as $user) {
            $notification = self::baseNotification(CustomerOrder::class, $orderID, $fromManagementID, $user->id);


            $notification->content = $management->name . ' đã xóa đơn hàng VN #' . $orderID;

            $notification->save();
        }
    }

    /**
     * @param int $orderID
     * @param int $toOfficerID
     * @param int $fromCustomerID
     */
    public static function deleteCustomerOrderByCustomer($orderID, $toOfficerID, $fromCustomerID)
    {
        $customer = Customer::findOrFail($fromCustomerID);

        foreach (User::where('role', '=', User::ROLE_ADMIN)
                     ->orWhere('role', '=', User::ROLE_CUSTOMER_SERVICE_MANAGEMENT)
                     ->orWhere('id', '=', $toOfficerID)
                     ->get() as $user) {
            $notification = self::baseNotification(CustomerOrder::class, $orderID, null, $user->id);

            $notification->content = 'Khách hàng ' . $customer->name . ' đã xóa đơn hàng VN #' . $orderID;

            $notification->save();
        }
    }
}