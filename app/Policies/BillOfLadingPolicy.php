<?php

namespace App\Policies;

use Modules\User\Models\User;
use Modules\BillOfLading\Models\BillOfLading;
use Illuminate\Auth\Access\HandlesAuthorization;

class BillOfLadingPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {
        if ($user->isAdmin() || $user->isCustomerServiceManagement() || $user->isVCL()) {
            return true;
        }
    }

    public function index(User $user)
    {
        return $user->isCustomerServiceOfficer();
    }

    public function approve(User $user)
    {
        return $user->isCustomerServiceManagement();
    }

    public function searchBySellerId(User $user)
    {
        return $user->isOrderingManagement() || $user->isAccountant();
    }

    public function chooseSellerId(User $user)
    {
        return $user->isOrderingManagement();
    }

    /**
     * Determine whether the user can view the bill of lading.
     *
     * @param  \Modules\User\Models\User $user
     * @param  \Modules\BillOfLading\Models\BillOfLading $billOfLading
     * @return mixed
     */
    public function view(User $user, BillOfLading $billOfLading = null)
    {
        /**
         * Customer Service Officer can view their bill of lading
         */
        if (!is_null($billOfLading) && $user->isCustomerServiceOfficer() && $user->id == $billOfLading->seller_id) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can create bill of ladings.
     *
     * @param  \Modules\User\Models\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isCustomerServiceOfficer();
    }

    /**
     * Determine whether the user can update the bill of lading.
     *
     * @param  \Modules\User\Models\User $user
     * @param  \Modules\BillOfLading\Models\BillOfLading $billOfLading
     * @return mixed
     */
    public function update(User $user, BillOfLading $billOfLading = null)
    {
        /**
         * Customer Service Officer can update their bill of lading
         */
        if (!is_null($billOfLading) && $user->isCustomerServiceOfficer() && $user->id == $billOfLading->seller_id) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the bill of lading.
     *
     * @return mixed
     */
    public function delete()
    {
        return false;
    }
}
