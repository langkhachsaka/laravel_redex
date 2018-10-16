<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnShipmentCodesToTasksAndTasksUpdatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('shipment_codes')->nullable();
        });
        Schema::table('tasks_updates', function (Blueprint $table) {
            $table->string('shipment_codes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tasks_and_tasks_updates', function (Blueprint $table) {

        });
    }
}
