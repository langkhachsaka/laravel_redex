<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(array(
            UsersTableSeeder::class,
            CustomerTableSeeder::class,
            ShopTableSeeder::class,
            AreaCodeTableSeeder::class,
            CustomerAddressTableSeeder::class,
            CustomerOrderSeeder::class,
            CourierCompanyTableSeeder::class,
            BillOfLadingTableSeeder::class,
            WarehouseTableSeeder::class,
            WarehouseReceivingCNTableSeeder::class,
            ComplaintTableSeeder::class,
            TransactionTableSeeder::class
        ));
    }
}
