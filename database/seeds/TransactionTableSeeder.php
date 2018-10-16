<?php
/**
 * Created by PhpStorm.
 * User: NguyenTri
 * Date: 30/05/2018
 * Time: 11:11 SA
 */

use Illuminate\Database\Seeder;

class TransactionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\Modules\Transaction\Models\Transaction::class, 20)->create();
    }
}