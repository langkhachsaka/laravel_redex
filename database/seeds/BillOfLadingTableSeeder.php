<?php
/**
 * Created by PhpStorm.
 * User: NguyenTri
 * Date: 04/05/2018
 * Time: 9:23 SA
 */

use Illuminate\Database\Seeder;

class BillOfLadingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\Modules\BillOfLading\Models\BillOfLading::class, 25)->create();
    }
}
