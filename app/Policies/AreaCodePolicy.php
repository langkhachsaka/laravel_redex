<?php

namespace App\Policies;

use Modules\User\Models\User;
use Modules\AreaCode\Models\AreaCode;
use Illuminate\Auth\Access\HandlesAuthorization;

class AreaCodePolicy
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

    public function index()
    {
        return false;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function list()
    {
        return true;
    }

    /**
     * Determine whether the user can view the area code.
     *
     * @return mixed
     */
    public function view()
    {
        return false;
    }

    /**
     * Determine whether the user can create area codes.
     *
     * @return mixed
     */
    public function create()
    {
        return false;
    }

    /**
     * Determine whether the user can update the area code.
     *
     * @return mixed
     */
    public function update()
    {
        return false;
    }

    /**
     * Determine whether the user can delete the area code.
     * @return mixed
     */
    public function delete()
    {
        return false;
    }
}
