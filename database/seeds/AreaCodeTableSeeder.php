<?php

use Illuminate\Database\Seeder;

class AreaCodeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('area_codes')->truncate();
        $arCode = array(
            array('code' => 'US', 'name' => 'United States'),
            array('code' => 'CA', 'name' => 'Canada'),
            array('code' => 'AF', 'name' => 'Afghanistan'),
            array('code' => 'AL', 'name' => 'Albania'),
            array('code' => 'DZ', 'name' => 'Algeria'),
            array('code' => 'AS', 'name' => 'American Samoa'),
            array('code' => 'AD', 'name' => 'Andorra'),
            array('code' => 'AO', 'name' => 'Angola'),
            array('code' => 'AI', 'name' => 'Anguilla'),
            array('code' => 'AQ', 'name' => 'Antarctica'),
            array('code' => 'AG', 'name' => 'Antigua and/or Barbuda'),
            array('code' => 'AR', 'name' => 'Argentina'),
            array('code' => 'AM', 'name' => 'Armenia'),
            array('code' => 'AW', 'name' => 'Aruba'),
            array('code' => 'AU', 'name' => 'Australia'),
            array('code' => 'AT', 'name' => 'Austria'),
            array('code' => 'AZ', 'name' => 'Azerbaijan'),
            array('code' => 'BS', 'name' => 'Bahamas'),
            array('code' => 'BH', 'name' => 'Bahrain'),
            array('code' => 'BD', 'name' => 'Bangladesh'),
            array('code' => 'BB', 'name' => 'Barbados'),
            array('code' => 'BY', 'name' => 'Belarus'),
            array('code' => 'BE', 'name' => 'Belgium'),
            array('code' => 'BZ', 'name' => 'Belize'),
            array('code' => 'BJ', 'name' => 'Benin'),
            array('code' => 'BM', 'name' => 'Bermuda'),
            array('code' => 'BT', 'name' => 'Bhutan'),
            array('code' => 'BO', 'name' => 'Bolivia'),
            array('code' => 'BA', 'name' => 'Bosnia and Herzegovina'),
            array('code' => 'BW', 'name' => 'Botswana'),
            array('code' => 'BV', 'name' => 'Bouvet Island'),
            array('code' => 'BR', 'name' => 'Brazil'),
            array('code' => 'IO', 'name' => 'British lndian Ocean Territory'),
            array('code' => 'BN', 'name' => 'Brunei Darussalam'),
            array('code' => 'BG', 'name' => 'Bulgaria'),
            array('code' => 'VU', 'name' => 'Vanuatu'),
            array('code' => 'VA', 'name' => 'Vatican City State'),
            array('code' => 'VE', 'name' => 'Venezuela'),
            array('code' => 'VN', 'name' => 'Vietnam'),
            array('code' => 'VG', 'name' => 'Virgin Islands (British)'),
            array('code' => 'VI', 'name' => 'Virgin Islands (U.S.)'),
            array('code' => 'WF', 'name' => 'Wallis and Futuna Islands'),
        );

        DB::table('area_codes')->insert($arCode);
    }
}
