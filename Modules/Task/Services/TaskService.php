<?php
namespace Modules\Task\Services;
use Modules\Task\Models\Task;
use Modules\CustomerOrder\Models\CustomerOrder;
use Modules\Complaint\Models\Complaint;
use Modules\User\Models\User;
use Modules\User\Models\UserRole;

class TaskService
{
    //Mapping with Order status and task status
    public static function statusCusOrderMapping($taskStatus)
    {
        switch ($taskStatus) {
            case Task::ORDER_INIT:
                return CustomerOrder::STATUS_PENDING;
                break;
            case Task::WAITTING_LATCHES_ORDER:
                return CustomerOrder::STATUS_PENDING;
                break;
            case Task::PROCESS_LATCHES_ORDER:
                return CustomerOrder::STATUS_PENDING;
                break;
            case Task::APPROVED_CUSTOMER_ORDER:
                return CustomerOrder::STATUS_APPROVED;
                break;
            case Task::CUSTOMER_DEPOSITED:
                return CustomerOrder::STATUS_DEPOSITED;
                break;
            case Task::CANCLE_CUSTOMER_ORDER:
                return CustomerOrder::STATUS_CANCELED;
                break;
            case Task::COMPLAINT_PENDING:
                return CustomerOrder::STATUS_COMPLAINT;
                break;
            case Task::COMPLAINT_ADMIN_PROCESSED:
                return CustomerOrder::STATUS_COMPLAINT;
                break;
            case Task::COMPLAINT_ORDERING_OFFICER_PROCESSED:
                return CustomerOrder::STATUS_COMPLAINT;
                break;
            case Task::ORDER_FINISHER:
                return CustomerOrder::STATUS_FINISHED;
                break;
            default:
                return -1;
        }
    }

    //Mapping with complaint status and task status
    public static function statusComplaintMapping($taskStatus)
    {
        switch ($taskStatus) {
            case Task::COMPLAINT_PENDING:
                return Complaint::STATUS_PENDING;
                break;
            case Task::COMPLAINT_ADMIN_PROCESSED:
                return Complaint::STATUS_PROCESSING;
                break;
            case Task::COMPLAINT_ORDERING_OFFICER_PROCESSED:
                return Complaint::STATUS_FINISHED;
                break;
            default:
                return -1;
        }
    }

    /**
     * get user type for display
     * @return int|null
     */
    public static function getUserType(){
        $userId = auth()->user()->id;
        $arrayForAdmin = array(Task::TYPE_ACCOUNTANT, Task::TYPE_ORDERING, Task::TYPE_CUSTOMER_SERVICE,
            Task::TYPE_DELIVERING_AND_RECEIVING, Task::TYPE_COMPLAINT, Task::TYPE_VERIFY_LADING_CODE,Task::TYPE_RECEIVE_SHIPMENT,Task::TYPE_DELIVERY);

        $arrayType = [];
        $userRoles = UserRole::where('user_id',$userId)->get();

        foreach ($userRoles as $userRole){
            switch ($userRole->role){
                case User::ROLE_CUSTOMER_SERVICE_MANAGEMENT:
                    array_push($arrayType,Task::TYPE_CUSTOMER_SERVICE);
                    break;
                case User::ROLE_CUSTOMER_SERVICE_OFFICER:
                    array_push($arrayType,Task::TYPE_CUSTOMER_SERVICE, Task::TYPE_COMPLAINT);
                    break;
                case User::ROLE_ORDERING_MANAGEMENT:
                    array_push($arrayType,Task::TYPE_ORDERING, Task::TYPE_COMPLAINT);
                    break;
                case User::ROLE_ORDERING_SERVICE_OFFICER:
                    array_push($arrayType,Task::TYPE_ORDERING, Task::TYPE_COMPLAINT);
                    break;
                case User::ROLE_DELIVERING_AND_RECEIVING_MANAGEMENT:
                    array_push($arrayType,Task::TYPE_DELIVERING_AND_RECEIVING);
                    break;
                case User::ROLE_CHINESE_SHIPPING_OFFICER:
                    array_push($arrayType,Task::TYPE_DELIVERING_AND_RECEIVING);
                    break;
                case User::ROLE_VIETNAMESE_SHIPPING_OFFICER:
                    array_push($arrayType,Task::TYPE_DELIVERING_AND_RECEIVING,Task::TYPE_VERIFY_LADING_CODE,Task::TYPE_RECEIVE_SHIPMENT, Task::TYPE_DELIVERY);
                    break;
                case User::ROLE_ACCOUNTANT:
                    array_push($arrayType,Task::TYPE_ACCOUNTANT, Task::TYPE_ORDERING);
                    break;
                case User::ROLE_ADMIN:
                    return $arrayForAdmin;
                break;

            }
        }
        return $arrayType;
    }

    /**
     * Get User Role name
     */
    public static function getUserRoleName($userRole){
        switch ($userRole){
            case User::ROLE_ADMIN:
                return 'Admin';
                break;
            case User::ROLE_CUSTOMER_SERVICE_MANAGEMENT:
                return 'Quản lý CSKH';
                break;
            case User::ROLE_ORDERING_MANAGEMENT:
                return 'Quản lý đặt hàng';
                break;
            case User::ROLE_DELIVERING_AND_RECEIVING_MANAGEMENT:
                return 'Quản lý bộ phận giao nhận';
                break;
            case User::ROLE_CUSTOMER_SERVICE_OFFICER:
                return 'Nhân viên CSKH';
                break;
            case User::ROLE_ORDERING_SERVICE_OFFICER:
                return 'Nhân viên đặt hàng';
                break;
            case User::ROLE_CHINESE_SHIPPING_OFFICER:
                return 'Nhân viên giao nhận TQ';
                break;
            case User::ROLE_VIETNAMESE_SHIPPING_OFFICER:
                return 'Nhân viên giao nhận VN';
                break;
            case User::ROLE_ACCOUNTANT:
                return 'Kế toán';
                break;
            default:
                return '';
        }
    }

    /**
     * Get user iD
     * @param $userID
     * @return role
     */
    public static function getUserRole($userID){
        $userRoles = UserRole::where('user_id',$userID)->select('role')->get();
        $roles = $userRoles->pluck('role');
        return $roles->toArray();
    }

}