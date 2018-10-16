<?php

namespace App\Policies;

use Modules\User\Models\User;
use Modules\Delivery\Models\Delivery;
use Illuminate\Auth\Access\HandlesAuthorization;

class DeliveryPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->isAdmin() || $user->isDeliveringAndReceivingManagement()) {
            return true;
        }
    }

    public function index(User $user)
    {
        return $user->isChineseShippingOfficer() || $user->isVietnameseShippingOfficer();
    }

    /**
     * Determine whether the user can view the delivery.
     *
     * @param  \Modules\User\Models\User  $user
     * @param  \Modules\Delivery\Models\Delivery  $delivery
     * @return mixed
     */
    public function view(User $user, Delivery $delivery = null)
    {
        if (is_null($delivery)
            || (!is_null($delivery) && ($user->isVietnameseShippingOfficer() || $user->isChineseShippingOfficer()))
        ) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can create deliveries.
     *
     * @param  \Modules\User\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can update the delivery.
     *
     * @param  \Modules\User\Models\User  $user
     * @param  \Modules\Delivery\Models\Delivery  $delivery
     * @return mixed
     */
    public function update(User $user, Delivery $delivery = null)
    {
        if (is_null($delivery)
            || (!is_null($delivery) && ($user->isVietnameseShippingOfficer() || $user->isChineseShippingOfficer()))
        ) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the delivery.
     *
     * @param  \Modules\User\Models\User  $user
     * @return mixed
     */
    public function delete(User $user)
    {
        return false;
    }
}
