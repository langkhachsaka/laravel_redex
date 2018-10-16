<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWarehouseVnLadingItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warehouse_vn_lading_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('warehouse_receiving_v_ns_id');
            $table->text('lading_code');
            $table->double('weight')->nullable();
            $table->double('height')->nullable();
            $table->double('width')->nullable();
            $table->double('length')->nullable();
            $table->unsignedTinyInteger('pack')->nullable();
            $table->unsignedInteger('other_fee')->nullable();
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
        Schema::dropIfExists('warehouse_vn_lading_items');
    }
}
