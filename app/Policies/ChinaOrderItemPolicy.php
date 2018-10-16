<?php

namespace App\Policies;

use Modules\User\Models\User;
use Modules\ChinaOrder\Models\ChinaOrderItem;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChinaOrderItemPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->isAdmin() || $user->isOrderingManagement()) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the china order item.
     *
     * @param  \Modules\User\Models\User $user
     * @return mixed
     */
    public function view(User $user)
    {
        return $user->isOrderingOfficer();
    }

    /**
     * Determine whether the user can create china order items.
     *
     * @param  \Modules\User\Models\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the china order item.
     *
     * @param  \Modules\User\Models\User $user
     * @return mixed
     */
    public function update(User $use)
    {
        //
    }

    /**
     * Determine whether the user can delete the china order item.
     *
     * @param  \Modules\User\Models\User $user
     * @return mixed
     */
    public function delete(User $user)
    {
        //
    }
}
