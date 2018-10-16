<?php

use Carbon\Carbon;
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

$factory->define(\Modules\CustomerOrder\Models\CustomerOrder::class, function (Faker $faker) {
    return [
        'status' => 0,
        'seller_id' => \Modules\User\Models\User::where('role', Modules\User\Models\User::ROLE_CUSTOMER_SERVICE_OFFICER)->get()->random()->id,
        'customer_id' => \Modules\Customer\Models\Customer::all()->random()->id,
        'customer_billing_name' => $faker->name,
        'customer_billing_address' => $faker->address,
        'customer_billing_phone' =>$faker->phoneNumber,
        'customer_shipping_name' => $faker->name,
        'customer_shipping_address' => $faker->address,
        'customer_shipping_phone' =>$faker->phoneNumber,
        'created_at' => Carbon::createFromTimestamp($faker->dateTimeBetween('-3 month', '+0 day')->getTimeStamp())
    ];
});
