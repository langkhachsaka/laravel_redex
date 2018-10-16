<?php
/**
 * Created by PhpStorm.
 * User: NguyenTri
 * Date: 04/05/2018
 * Time: 7:56 SA
 */

use Illuminate\Database\Seeder;

class WarehouseReceivingCNTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\Modules\WarehouseReceivingCN\Models\WarehouseReceivingCN::class, 25)->create();
    }
}
