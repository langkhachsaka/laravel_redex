<?php

use Illuminate\Database\Seeder;

class CustomerOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\Modules\CustomerOrder\Models\CustomerOrder::class, 150)->create();
    }
}
