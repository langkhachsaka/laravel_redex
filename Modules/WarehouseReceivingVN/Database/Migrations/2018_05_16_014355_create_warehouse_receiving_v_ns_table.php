<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWarehouseReceivingVNsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warehouse_receiving_v_ns', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('warehouse_id')->nullable();
            $table->dateTime('date_receiving')->nullable();
            $table->text('shipment_code')->nullable();
            $table->double('weight')->nullable();
            $table->double('height')->nullable();
            $table->double('width')->nullable();
            $table->double('length')->nullable();
            $table->unsignedInteger('user_receive_id')->nullable();
            $table->unsignedTinyInteger('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('warehouse_receiving_v_ns');
    }
}
