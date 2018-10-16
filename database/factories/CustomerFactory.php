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

$factory->define(\Modules\Customer\Models\Customer::class, function (Faker $faker) {
    return [
        'name' => 'KH ' . $faker->name,
        'username' => $faker->userName,
//        'phone' => $faker->phoneNumber,
//        'address' => $faker->streetAddress,
        'email' => $faker->email,
        'password' => '$2y$10$86Ua846EsOlT4O4RHv.nUOZnHhc058eFoIdQ4kc2Puez.pRW9fhjm', /*123456*/
        'remember_token' => str_random(10),
    ];
});
