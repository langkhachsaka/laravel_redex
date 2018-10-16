<?php

namespace App\Policies;

use Modules\User\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RatePolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {
        if ($user->isAdmin() || $user->isVCL()) {
            return true;
        }
//        return false;
    }

    public function index()
    {
        return true;
    }

    public function view()
    {
        return false;
    }

    public function create()
    {
        return true;
    }

    public function update()
    {
        return true;
    }

    public function delete()
    {
        return true;
    }
}
