<?php
/**
 * Created by PhpStorm.
 * User: CongHD
 * Date: 16/05/2018
 * Time: 7:48 SA
 */

use Faker\Generator as Faker;

$factory->define(\Modules\WarehouseReceivingVN\Models\WarehouseReceivingVN::class, function (Faker $faker) {
    return [
        'weight' => rand(10, 50),
        'date_receiving' => \Carbon\Carbon::now(),
        'warehouse_id' => rand(1, 20),
        'height' => rand(10, 50),
        'width' => rand(10, 50),
        'length' => rand(10, 50),
        'shipment_code' => 'BT' . rand(10000, 50000),
        'status' => rand(1, 2),
    ];
});
