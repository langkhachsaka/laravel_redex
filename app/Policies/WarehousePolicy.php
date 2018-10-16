<?php

namespace App\Policies;

use Modules\User\Models\User;
use Modules\Warehouse\Models\Warehouse;
use Illuminate\Auth\Access\HandlesAuthorization;

class WarehousePolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {
        if ($user->isAdmin() || $user->isVCL()) {
            return true;
        }
    }

    public function index()
    {
        return false;
    }

    public function list(User $user)
    {
        return true;
    }
    /**
     * Determine whether the user can view the warehouse.
     * @return mixed
     */
    public function view()
    {
        return false;
    }

    /**
     * Determine whether the user can create warehouses.
     *
     * @return mixed
     */
    public function create()
    {
        return false;
    }

    /**
     * Determine whether the user can update the warehouse.
     *
     * @return mixed
     */
    public function update()
    {
        return false;
    }

    /**
     * Determine whether the user can delete the warehouse.
     *
     * @return mixed
     */
    public function delete()
    {
        return false;
    }
}
