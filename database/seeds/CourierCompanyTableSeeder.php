<?php

use Illuminate\Database\Seeder;

class CourierCompanyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //DB::table('courier_companies')->truncate();
        $courierCompanies = [
            [
                'name' => '安能物流',
            ],
            [
                'name' => '百世快运包裹 /平邮邦送物流',
            ],
            [
                'name' => 'DHL 德邦物流 递四方',
            ],
            [
                'name' => 'EMS 国际件',
            ],
            [
                'name' => 'FedEx',
            ],
            [
                'name' => '国通快递',
            ],
            [
                'name' => '佳吉快运 京广快递',
            ],
            [
                'name' => '快捷速递 ',
            ],
            [
                'name' => '龙邦快运 联邦快递 联昊通',
            ],
            [
                'name' => '能达速递',
            ],
            [
                'name' => '全峰快递',
            ],
            [
                'name' => '如风达',
            ],
            [
                'name' => '速尔快递 申通快递 顺丰速运',
            ],
            [
                'name' => 'TNT 天地华宇 天天快递',
            ],
            [
                'name' => 'USPS UPS',
            ],
            [
                'name' => '新邦物流 信丰物流 ',
            ],
            [
                'name' => '优速快递 韵达快递',
            ],
            [
                'name' => '圆通快递',
            ]
        ];

        DB::table('courier_companies')->insert($courierCompanies);
    }
}
