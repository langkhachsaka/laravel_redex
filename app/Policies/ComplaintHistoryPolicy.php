<?php

namespace App\Policies;

use Modules\User\Models\User;
use Modules\Complaint\Models\ComplaintHistory;
use Illuminate\Auth\Access\HandlesAuthorization;

class ComplaintHistoryPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->isAdmin() || $user->isCustomerServiceManagement()) {
            return true;
        }
    }

    public function index(User $user)
    {
        if ($user->isCustomerServiceOfficer()) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the complaint history.
     *
     * @param  \Modules\User\Models\User  $user
     * @return mixed
     */
    public function view(User $user)
    {
        if ($user->isCustomerServiceOfficer()) {
            return true;
        }
    }

    /**
     * Determine whether the user can create complaint histories.
     *
     * @param  \Modules\User\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        if ($user->isCustomerServiceOfficer()) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the complaint history.
     *
     * @param  \Modules\User\Models\User  $user
     * @return mixed
     */
    public function update(User $user)
    {
        if ($user->isCustomerServiceOfficer()) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the complaint history.
     *
     * @param  \Modules\User\Models\User  $user
     * @return mixed
     */
    public function delete(User $user)
    {
        //
    }
}
