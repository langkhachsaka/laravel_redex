<?php
/**
 * Created by PhpStorm.
 * User: CongHD
 * Date: 16/05/2018
 * Time: 7:56 SA
 */

use Illuminate\Database\Seeder;

class WarehouseReceivingVNTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\Modules\WarehouseReceivingVN\Models\WarehouseReceivingVN::class, 25)->create();
    }
}
