<?php
/**
 * Created by PhpStorm.
 * User: NguyenTri
 * Date: 30/05/2018
 * Time: 11:08 SA
 */

use Faker\Generator as Faker;


$factory->define(\Modules\Transaction\Models\Transaction::class, function (Faker $faker) {
    return [

        'transactiontable_id' => \Modules\CustomerOrder\Models\CustomerOrder::all()->random()->id,
        'transactiontable_type' => \Modules\CustomerOrder\Models\CustomerOrder::class,
        'money' => rand(100000, 9000000),
        'note' => $faker->text(),
        'type' => 0,
        'user_id' => \Modules\User\Models\User::where('role', \Modules\User\Models\User::ROLE_ACCOUNTANT)->get()->random()->id,
    ];
});
