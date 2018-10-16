<?php
/**
 * Created by PhpStorm.
 * User: NguyenTri
 * Date: 14/05/2018
 * Time: 10:46 SA
 */

use Faker\Generator as Faker;

$factory->define(\Modules\Notification\Models\Notification::class, function (Faker $faker) {
    return [
        'notificationtable_id' => \Modules\CustomerOrder\Models\CustomerOrder::all()->random()->id,
        'notificationtable_type' => \Modules\CustomerOrder\Models\CustomerOrder::class,
        'content' => $faker->text(100),
        'from_user_id' => \Modules\User\Models\User::all()->random()->id,
        'to_user_id' => \Modules\User\Models\User::where(
            'role',
            '=',
            Modules\User\Models\User::ROLE_CUSTOMER_SERVICE_OFFICER
        )
            ->get()->random()->id,

        'is_read' => 0
    ];
});
