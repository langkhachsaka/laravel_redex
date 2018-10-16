<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWarehouseReceivingCNsTable extends Migration
{   
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warehouse_receiving_c_ns', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('warehouse_id')->nullable();
            $table->dateTime('date_receiving')->nullable();
            $table->double('weight')->nullable();
            $table->double('height')->nullable();
            $table->double('width')->nullable();
            $table->double('length')->nullable();
            $table->text('note')->nullable();
            $table->string('bill_of_lading_code')->nullable();
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
        Schema::dropIfExists('warehouse_receiving_c_ns');
    }
}
