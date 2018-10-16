<?php
/**
 * Created by PhpStorm.
 * User: NguyenTri
 * Date: 04/05/2018
 * Time: 7:48 SA
 */

use Faker\Generator as Faker;

$factory->define(\Modules\WarehouseReceivingCN\Models\WarehouseReceivingCN::class, function (Faker $faker) {
    return [
        'date_receiving' => \Carbon\Carbon::now(),
        'weight' => rand(10, 50),
        'warehouse_id' => rand(1, 10),
        'height' => rand(10, 50),
        'width' => rand(10, 50),
        'length' => rand(10, 50),
        'user_receive_id' => rand(1, 20),
        'status' => rand(1, 2),
    ];
});
