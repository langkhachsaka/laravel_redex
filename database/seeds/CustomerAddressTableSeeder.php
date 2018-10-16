<?php

use Illuminate\Database\Seeder;

class CustomerAddressTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('customer_addresses')->insert([
            'name' => 'Nguyễn Bảo Trung',
            'phone' => '0917549555',
            'address' => 'Số 7 ngõ 13 Nguyễn Sĩ Sách, TP Vinh, Nghệ An',
            'customer_id' => 1,
            'is_default' => 1
        ]);
        factory(\Modules\Customer\Models\CustomerAddress::class, 25)->create();
    }
}
