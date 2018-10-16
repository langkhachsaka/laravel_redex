<?php
/**
 * Created by PhpStorm.
 * User: NguyenTri
 * Date: 11/05/2018
 * Time: 10:20 SA
 */

use Faker\Generator as Faker;

$factory->define(\Modules\Complaint\Models\Complaint::class, function (Faker $faker) {
    return [
        'ordertable_id' => \Modules\Customer\Models\Customer::all()->random()->id,
        'ordertable_type' => \Modules\CustomerOrder\Models\CustomerOrder::class,
        'title' => $faker->text(100),
        'content' => $faker->text,
        'status' => 0,
        'date_end_expected' => $faker->date(),
        'user_id' => \Modules\User\Models\User::where('role', '=', 30)->get()->random()->id,
        'customer_id' => \Modules\Customer\Models\Customer::all()->random()->id
    ];
});
