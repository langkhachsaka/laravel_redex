<?php

namespace App\Policies;

use Modules\User\Models\User;
use Modules\ChinaOrder\Models\ChinaOrder;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChinaOrderPolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @param $ability
     * @return bool
     */
    public function before(User $user, $ability)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }

    /**
     * @param User $user
     * @return bool
     */
    public function index(User $user)
    {
        return $user->isOrderingOfficer() || $user->isOrderingManagement() || $user->isAccountant();
    }

    /**
     * @param User $user
     * @return bool
     */
    public function approve(User $user)
    {
        return $user->isOrderingManagement();
    }

    /**
     * @param User $user
     * @return bool
     */
    public function searchByUserPurchasingId(User $user)
    {
        return $user->isOrderingManagement() || $user->isAccountant();
    }

    /**
     * @param User $user
     * @return bool
     */
    public function chooseUserPurchasingId(User $user)
    {
        return $user->isOrderingManagement();
    }


    /**
     * Determine whether the user can view the china order.
     *
     * @param  \Modules\User\Models\User $user
     * @param  \Modules\ChinaOrder\Models\ChinaOrder $chinaOrder
     * @return mixed
     */
    public function view(User $user, ChinaOrder $chinaOrder = null)
    {
        /**
         * Customer Service Officer can view their China Order
         * Customer Service Management can view all China Order
         */
        if ((is_null($chinaOrder) && $user->isOrderingOfficer())
            ||(!is_null($chinaOrder) && $user->isOrderingOfficer() && $user->id == $chinaOrder->user_purchasing_id)
            || $user->isOrderingManagement()
            || $user->isAccountant()
        ) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can create china orders.
     *
     * @param  \Modules\User\Models\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isOrderingManagement();
    }

    /**
     * Determine whether the user can update the china order.
     *
     * @param  \Modules\User\Models\User $user
     * @return mixed
     */
    public function update(User $user)
    {
        /**
         * Customer Service Officer can update their China Order
         * Customer Service Management can update all China Order
         */
        if ($user->isOrderingManagement()) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the china order.
     *
     * @param  \Modules\User\Models\User $user
     * @return mixed
     */
    public function delete(User $user)
    {
        return $user->isOrderingManagement();
    }
}
