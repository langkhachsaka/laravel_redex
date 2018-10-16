<?php
/**
 * Created by PhpStorm.
 * User: NguyenTri
 * Date: 04/05/2018
 * Time: 8:03 SA
 */

use Faker\Generator as Faker;

$factory->define(\Modules\BillOfLading\Models\BillOfLading::class, function (Faker $faker) {
    return [
        'file_path' => $faker->url,
        'customer_id' => \Modules\Customer\Models\Customer::all()->random()->id,
        'customer_billing_name' => $faker->name,
        'customer_billing_address' => $faker->address,
        'customer_billing_phone' =>$faker->phoneNumber,
        'customer_shipping_name' => $faker->name,
        'customer_shipping_address' => $faker->address,
        'customer_shipping_phone' =>$faker->phoneNumber,
        'courier_company_id' => \Modules\CourierCompany\Models\CourierCompany::all()->random()->id,
        'seller_id' => \Modules\User\Models\User::where('role', '=', 30)->get()->random()->id,
        'status' => 0,
    ];
});
