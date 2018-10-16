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

$factory->define(\Modules\User\Models\User::class, function (Faker $faker) {
    $roles = [10,20,21,40,22,30,31,32,33];
    $rand_keys = array_rand($roles, 2);
    return [
        'name' => 'NV ' . $faker->name,
        'username' => $faker->userName,
        'phone' => $faker->phoneNumber,
        'role' => $roles[$rand_keys[0]],
        'email' => $faker->email,
        'password' => '$2y$10$86Ua846EsOlT4O4RHv.nUOZnHhc058eFoIdQ4kc2Puez.pRW9fhjm', /*123456*/
        'remember_token' => str_random(10),
    ];
});
