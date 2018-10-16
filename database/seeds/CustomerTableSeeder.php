<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('customers')->insert([
            'username' => 'trungnb',
            'name' => 'Nguyễn Bảo Trung',
            'email' => 'trungnguyen.utc.khmt@gmail.com',
            'password' => bcrypt('123456'),
        ]);

        factory(\Modules\Customer\Models\Customer::class, 25)->create();
    }
}
