<?php

namespace App\Policies;

use Modules\User\Models\User;
use Modules\Complaint\Models\Complaint;
use Illuminate\Auth\Access\HandlesAuthorization;

class ComplaintPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
//        return true;
        if ($user->isAdmin() || $user->isCustomerServiceOfficer() || $user->isOrderingOfficer()) {
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
     * Determine whether the user can view the complaint.
     *
     * @param  \Modules\User\Models\User  $user
     * @param  \Modules\Complaint\Models\Complaint  $complaint
     * @return mixed
     */
    public function view(User $user, Complaint $complaint = null)
    {
        if (is_null($complaint) || (!is_null($complaint) && $user->id == $complaint->user_id)) {
            return true;
        }
    }

    /**
     * Determine whether the user can create complaints.
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
     * Determine whether the user can update the complaint.
     *
     * @param  \Modules\User\Models\User  $user
     * @param  \Modules\Complaint\Models\Complaint  $complaint
     * @return mixed
     */
    public function update(User $user, Complaint $complaint = null)
    {
        if (is_null($complaint) || (!is_null($complaint) && $user->id == $complaint->user_id)) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the complaint.
     *
     * @param  \Modules\User\Models\User  $user
     * @return mixed
     */
    public function delete(User $user)
    {
        //
    }
}
