<?php

namespace App\Policies;

use Modules\User\Models\User;
use Modules\Customer\Models\Customer;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {
        if ($user->isAdmin() || $user->isCustomerServiceManagement() || $user->isVCL()) {
            return true;
        }
    }

    public function updatePassword()
    {
        return false;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function index(User $user)
    {
        return $user->isCustomerServiceOfficer();
    }

    /**
     * @param User $user
     * @return bool
     */
    public function list(User $user)
    {
        return $user->isCustomerServiceOfficer() || $user->isCustomerServiceManagement() || $user->isAccountant();
    }

    /**
     * Determine whether the user can view the customer.
     *
     * @param User $user
     * @return mixed
     */
    public function view(User $user)
    {
        return $user->isCustomerServiceOfficer();
    }

    /**
     * Determine whether the user can create customers.
     *
     * @param User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isCustomerServiceOfficer();
    }

    /**
     * Determine whether the user can update the customer.
     *
     * @param User $user
     * @return mixed
     */
    public function update(User $user)
    {
        return $user->isCustomerServiceOfficer();
    }

    /**
     * Determine whether the user can delete the customer.
     *
     * @return mixed
     */
    public function delete()
    {
        return false;
    }
}
