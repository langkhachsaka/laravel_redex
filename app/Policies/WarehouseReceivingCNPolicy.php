<?php

namespace App\Policies;

use Modules\User\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\WarehouseReceivingCN\Models\WarehouseReceivingCN;

class WarehouseReceivingCNPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->isAdmin() || $user->isDeliveringAndReceivingManagement() || $user->isVCL()) {
            return true;
        }
    }

    public function index(User $user)
    {
        if ($user->isChineseShippingOfficer()) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the warehouse receiving.
     *
     * @param User $user
     * @param WarehouseReceivingCN|null $warehouseRecivingCN
     * @return mixed
     */
    public function view(User $user, WarehouseReceivingCN $warehouseRecivingCN = null)
    {
        if (!is_null($warehouseRecivingCN) && $user->isChineseShippingOfficer() && $user->id == $warehouseRecivingCN->user_receive_id) {
            return true;
        }
    }

    /**
     * Determine whether the user can create warehouse receivings.
     *
     * @return mixed
     */
    public function create(User $user)
    {
        if ($user->isChineseShippingOfficer()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can update the warehouse receiving.
     *
     * @return mixed
     */
    public function update(User $user)
    {
        if ($user->isChineseShippingOfficer()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can delete the warehouse receiving.
     *
     * @return mixed
     */
    public function delete()
    {
        return false;
    }
}
