<?php

namespace App\Policies;

use Modules\User\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LadingCodePolicy
{
    use HandlesAuthorization;

    /**
     * @param $user
     * @return bool
     */
    public function before(User $user, $ability)
    {
        if ($user->isAdmin() || $user->isVCL()) {
            return true;
        }
    }

    public function index()
    {
        return true;
    }

    /**
     * Determine whether the user can view the shop.
     *
     * @return mixed
     */
    public function view()
    {
        return true;
    }

    /**
     * Determine whether the user can create shops.
     *
     * @return mixed
     */
    public function create()
    {
        return true;
    }

    /**
     * Determine whether the user can update the shop.
     *
     * @return mixed
     */
    public function update()
    {
        return true;
    }

    /**
     * Determine whether the user can delete the shop.
     *
     * @return mixed
     */
    public function delete()
    {
        return true;
    }
}
