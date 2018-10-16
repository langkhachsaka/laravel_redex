<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChinaOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('china_order_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('china_order_id');
            $table->unsignedInteger('customer_order_item_id');
            $table->unsignedInteger('quantity')->nullable(1);
            $table->unsignedInteger('status')->nullable();
            $table->decimal('price_cny', 15, 2)->nullable();

            $table->foreign('china_order_id')->references('id')->on('china_orders')->onDelete('cascade');
            $table->foreign('customer_order_item_id')
                ->references('id')
                ->on('customer_order_items')
                ->onDelete('cascade');
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
        Schema::dropIfExists('china_order_items');
    }
}
