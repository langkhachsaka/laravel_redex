<?php

use Illuminate\Database\Seeder;

class NotificationTableSedder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\Modules\Notification\Models\Notification::class, 20)->create();
    }
}
