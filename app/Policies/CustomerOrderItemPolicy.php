<?php

namespace App\Policies;

use Modules\User\Models\User;
use Modules\CustomerOrder\Models\CustomerOrderItem;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerOrderItemPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->isAdmin() || $user->isCustomerServiceOfficer() || $user->isCustomerServiceManagement() || $user->isVCL()) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can view the customer order item.
     *
     * @param  \Modules\User\Models\User  $user
     * @param  \Modules\CustomerOrder\Models\CustomerOrderItem  $customerOrderItem
     * @return mixed
     */
    public function view(User $user, CustomerOrderItem $customerOrderItem)
    {
        //
    }

    /**
     * Determine whether the user can create customer order items.
     *
     * @param  \Modules\User\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the customer order item.
     *
     * @param  \Modules\User\Models\User  $user
     * @param  \Modules\CustomerOrder\Models\CustomerOrderItem  $customerOrderItem
     * @return mixed
     */
    public function update(User $user, CustomerOrderItem $customerOrderItem)
    {
        //
    }

    /**
     * Determine whether the user can delete the customer order item.
     *
     * @param  \Modules\User\Models\User  $user
     * @param  \Modules\CustomerOrder\Models\CustomerOrderItem  $customerOrderItem
     * @return mixed
     */
    public function delete(User $user, CustomerOrderItem $customerOrderItem)
    {
        //
    }
}
