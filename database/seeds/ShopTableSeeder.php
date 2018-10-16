<?php

use Illuminate\Database\Seeder;

class ShopTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\Modules\Shop\Models\Shop::class, 30)->create();
        /*DB::table('shops')->truncate();
        $shop = [
            [
                'name' => 'TAOBAO',
                'link' => 'http://google.com',
            ],
            [
                'name' => '1088',
                'link' => 'http://google.com',
            ],
        ];

        DB::table('shops')->insert($shop);
        */
    }
}
