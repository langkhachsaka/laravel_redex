<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('shipment_code');
            $table->double('weight')->nullable();
            $table->double('height')->nullable();
            $table->double('width')->nullable();
            $table->double('length')->nullable();
            $table->unsignedInteger('creator_id')->nullable();
            $table->unsignedInteger('warehouse_id')->nullable();
            $table->unsignedTinyInteger('status')->nullable();
            $table->unsignedTinyInteger('transport_type')->nullable();
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
        Schema::dropIfExists('shipments');
    }
}
