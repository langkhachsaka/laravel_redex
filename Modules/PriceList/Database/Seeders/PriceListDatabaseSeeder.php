<?php

namespace Modules\PriceList\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class PriceListDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('price_lists')->insert([
            [
                'key' => 'less_than_half',
                'description' => 'Từ 0,5kg trở xuống',
                'price' => 40000,
                'delivery_type' => 1
            ],
            [
                'key' => 'more_than_half',
                'description' => 'Trên 0,5kg',
                'price' => 70000,
                'delivery_type' => 1
            ],
            [
                'key' => 'more_than_5',
                'description' => 'Từ 5kg trở lên',
                'price' => 55000,
                'delivery_type' => 1
            ],
            [
                'key' => 'more_than_30',
                'description' => 'Từ 30kg trở lên',
                'price' => 50000,
                'delivery_type' => 1
            ],
            [
                'key' => 'less_than_30_is_wholesale',
                'description' => 'Dưới 30kg',
                'price' => 50000,
                'delivery_type' => 1
            ],
            [
                'key' => 'more_than_30_is_wholesale',
                'description' => 'Từ 30kg trở lên',
                'price' => 50000,
                'delivery_type' => 1
            ],
            [
                'key' => 'less_than_30_normal',
                'description' => 'Dưới 30kg',
                'price' => 35000,
                'delivery_type' => 0
            ],
            [
                'key' => 'more_than_30_normal',
                'description' => 'Từ 30kg trở lên',
                'price' => 30000,
                'delivery_type' => 0
            ]
        ]);

        // $this->call("OthersTableSeeder");
    }
}
