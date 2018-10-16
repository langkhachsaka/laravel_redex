<?php

namespace App\Policies;

use Modules\User\Models\User;
use Modules\CourierCompany\Models\CourierCompany;
use Illuminate\Auth\Access\HandlesAuthorization;

class CourierCompanyPolicy
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

    /**
     * Determine whether the user can view the courier company.
     *
     * @return mixed
     */
    public function view()
    {
        return false;
    }

    /**
     * Determine whether the user can create courier companies.
     *
     * @return mixed
     */
    public function create()
    {
        return false;
    }

    /**
     * Determine whether the user can update the courier company.
     *
     * @return mixed
     */
    public function update()
    {
        return false;
    }

    /**
     * Determine whether the user can delete the courier company.
     *
     * @return mixed
     */
    public function delete()
    {
        return false;
    }
}
