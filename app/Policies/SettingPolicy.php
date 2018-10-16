<?php

namespace App\Policies;

use Modules\User\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SettingPolicy
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
        return false;
    }

    /**
     * Determine whether the user can update the shop.
     *
     * @return mixed
     */
    public function update()
    {
        return false;
    }

}
