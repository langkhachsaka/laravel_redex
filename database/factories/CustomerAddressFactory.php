<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(\Modules\Customer\Models\CustomerAddress::class, function (Faker $faker) {
    return [
        'name' => 'KH ' . $faker->name,
        'phone' => $faker->phoneNumber,
        'address' => $faker->streetAddress,
        'customer_id' => \Modules\Customer\Models\Customer::all()->random()->id,
        'is_default' => 1
    ];
});
