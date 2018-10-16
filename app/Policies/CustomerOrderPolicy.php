<?php

namespace App\Policies;

use Modules\User\Models\User;
use Modules\CustomerOrder\Models\CustomerOrder;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerOrderPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->isAdmin() || $user->isVCL()) {
            return true;
        }
    }

    public function index(User $user)
    {
        return $user->isCustomerServiceOfficer() || $user->isCustomerServiceManagement() || $user->isAccountant() || $user->isOrderingOfficer() || $user->isOrderingOfficer();
    }

    public function approve(User $user)
    {
        return $user->isCustomerServiceManagement() || $user->isAdmin();
    }

    public function searchBySellerId(User $user)
    {
        return $user->isCustomerServiceManagement() || $user->isAccountant();
    }

    public function updateSellerId(User $user)
    {
        return $user->isCustomerServiceManagement();
    }

    /**
     * Determine whether the user can view the customer order.
     *
     * @param  \Modules\User\Models\User $user
     * @param  \Modules\CustomerOrder\Models\CustomerOrder $customerOrder
     * @return mixed
     */
    public function view(User $user, CustomerOrder $customerOrder = null)
    {
        /**
         * Customer Service Officer can view their Customer Order
         * Customer Service Management can view all Customer Order
         */
        if ((is_null($customerOrder) && $user->isCustomerServiceOfficer())
            ||(!is_null($customerOrder) && $user->isCustomerServiceOfficer() && $user->id == $customerOrder->seller_id)
            || $user->isCustomerServiceManagement()
            || $user->isAccountant()
            || $user->isOrderingOfficer()
        ) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can create customer orders.
     *
     * @param  \Modules\User\Models\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isCustomerServiceOfficer() || $user->isCustomerServiceManagement();
    }

    /**
     * Determine whether the user can update the customer order.
     *
     * @param  \Modules\User\Models\User $user
     * @param  \Modules\CustomerOrder\Models\CustomerOrder $customerOrder
     * @return mixed
     */
    public function update(User $user, CustomerOrder $customerOrder = null)
    {
        /**
         * Customer Service Officer can update their Customer Order
         * Customer Service Management can update all Customer Order
         */
        if ((is_null($customerOrder) && $user->isCustomerServiceOfficer())
            || (!is_null($customerOrder) && $user->isCustomerServiceOfficer() && $user->id == $customerOrder->seller_id)
            || $user->isCustomerServiceManagement()|| $user->isOrderingOfficer()) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the customer order.
     *
     * @param  \Modules\User\Models\User $user
     * @return mixed
     */
    public function delete(User $user)
    {
        return $user->isCustomerServiceManagement();
    }
}
