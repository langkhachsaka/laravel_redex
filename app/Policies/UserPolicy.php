<?php

namespace App\Policies;

use Modules\User\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
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
        return $user->isCustomerServiceManagement()
            || $user->isOrderingManagement()
            || $user->isDeliveringAndReceivingManagement()
            || $user->isAccountant();
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return mixed
     */
    public function view()
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     *
     * @return mixed
     */
    public function create()
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \Modules\User\Models\User  $user
     * @param  \Modules\User\Models\User  $model
     * @return mixed
     */
    public function update(User $user, User $model = null)
    {
        /** User can update their Account */
        return is_null($model) ? false : $user->id === $model->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return mixed
     */
    public function delete()
    {
        return false;
    }
}
