<?php

namespace App\Policies;

use Modules\User\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ShopPolicy
{
    use HandlesAuthorization;

    /**
     * @param $user
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
    public function list(User $user)
    {
        return $user->isCustomerServiceOfficer()
            || $user->isCustomerServiceManagement()
            || $user->isDeliveringAndReceivingManagement()
            || $user->isVietnameseShippingOfficer()
            || $user->isChineseShippingOfficer();
    }

    /**
     * Determine whether the user can view the shop.
     *
     * @return mixed
     */
    public function view()
    {
        return false;
    }

    /**
     * Determine whether the user can create shops.
     *
     * @return mixed
     */
    public function create()
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

    /**
     * Determine whether the user can delete the shop.
     *
     * @return mixed
     */
    public function delete()
    {
        return false;
    }
}
