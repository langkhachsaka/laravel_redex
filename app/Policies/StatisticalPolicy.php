<?php

namespace App\Policies;

use Modules\User\Models\User;
use Modules\Statistical\Models\Statistical;
use Illuminate\Auth\Access\HandlesAuthorization;

class StatisticalPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {
        return true;
    }

    public function index()
    {

    }

    /**
     * Determine whether the user can view the statistical.
     *
     * @param  \Modules\User\Models\User  $user
     * @param  \Modules\Statistical\Models\Statistical  $statistical
     * @return mixed
     */
    public function view(User $user, Statistical $statistical)
    {
        //
    }

    /**
     * Determine whether the user can create statisticals.
     *
     * @param  \Modules\User\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the statistical.
     *
     * @param  \Modules\User\Models\User  $user
     * @param  \Modules\Statistical\Models\Statistical $statistical
     * @return mixed
     */
    public function update(User $user, Statistical $statistical)
    {
        //
    }

    /**
     * Determine whether the user can delete the statistical.
     *
     * @param  \Modules\User\Models\User  $user
     * @param  \Modules\Statistical\Models\Statistical  $statistical
     * @return mixed
     */
    public function delete(User $user, Statistical $statistical)
    {
        //
    }
}
