<?php

namespace App\Policies;

use Modules\User\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\WarehouseReceivingVN\Models\WarehouseReceivingVN;

class WarehouseReceivingVNPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {
        if ($user->isAdmin() || $user->isDeliveringAndReceivingManagement() || $user->isVCL()) {
            return true;
        }
    }

    public function index(User $user)
    {
        if ($user->isVietnameseShippingOfficer()) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the warehouse reciving v n.
     *
     * @param  \Modules\User\Models\User $user
     * @param  \Modules\WarehouseReceivingVN\Models\WarehouseReceivingVN $warehouseRecivingVN
     * @return mixed
     */
    public function view(User $user, WarehouseReceivingVN $warehouseRecivingVN = null)
    {
        if (!is_null($warehouseRecivingVN) && $user->isVietnameseShippingOfficer() && $user->id == $warehouseRecivingVN->user_receive_id) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can create warehouse reciving v ns.
     *
     * @param  \Modules\User\Models\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the warehouse reciving v n.
     *
     * @param  \Modules\User\Models\User $user
     * @param  \Modules\WarehouseReceivingVN\Models\WarehouseReceivingVN $warehouseRecivingVN
     * @return mixed
     */
    public function update(User $user, WarehouseReceivingVN $warehouseRecivingVN = null)
    {
        if (!is_null($warehouseRecivingVN) && $user->isVietnameseShippingOfficer() && $user->id == $warehouseRecivingVN->user_receive_id) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the warehouse reciving v n.
     *
     * @param  \Modules\User\Models\User $user
     * @param  \Modules\WarehouseReceivingVN\Models\WarehouseReceivingVN $warehouseRecivingVN
     * @return mixed
     */
    public function delete(User $user, WarehouseReceivingVN $warehouseRecivingVN = null)
    {
        if ($user->isAdmin() || $user->isDeliveringAndReceivingManagement() || $user->isVietnameseShippingOfficer()) {
            return true;
        }
    }
}
