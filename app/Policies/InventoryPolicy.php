<?php

namespace App\Policies;

use Modules\User\Models\User;
use Modules\Inventory\Models\Inventory;
use Illuminate\Auth\Access\HandlesAuthorization;

class InventoryPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->isAdmin()
            || $user->isDeliveringAndReceivingManagement()
            || $user->isChineseShippingOfficer()
            || $user->isVietnameseShippingOfficer()
        ) {
            return true;
        }
        return false;
    }

    public function index(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the inventory.
     *
     * @param  \Modules\User\Models\User  $user
     * @param  \Modules\Inventory\Models\Inventory  $inventory
     * @return mixed
     */
    public function view(User $user, Inventory $inventory)
    {
        //
    }

    /**
     * Determine whether the user can create inventories.
     *
     * @param  \Modules\User\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the inventory.
     *
     * @param  \Modules\User\Models\User  $user
     * @param  \Modules\Inventory\Models\Inventory  $inventory
     * @return mixed
     */
    public function update(User $user, Inventory $inventory)
    {
        //
    }

    /**
     * Determine whether the user can delete the inventory.
     *
     * @param  \Modules\User\Models\User  $user
     * @param  \Modules\Inventory\Models\Inventory  $inventory
     * @return mixed
     */
    public function delete(User $user, Inventory $inventory)
    {
        //
    }
}
