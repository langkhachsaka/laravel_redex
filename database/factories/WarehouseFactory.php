<?php
/**
 * Created by PhpStorm.
 * User: NguyenTri
 * Date: 04/05/2018
 * Time: 10:13 SA
 */

use Faker\Generator as Faker;

$factory->define(Modules\Warehouse\Models\Warehouse::class, function (Faker $faker) {
    return [
        'name' => 'Kho '.$faker->userName,
        'address' => $faker->streetAddress,
        'type' => rand(1, 2)
    ];
});
