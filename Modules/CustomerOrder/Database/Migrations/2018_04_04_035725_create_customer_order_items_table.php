<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_order_items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('image')->nullable();
            $table->string('description')->nullable();
            $table->text('link')->nullable();
            $table->string('size')->nullable();
            $table->string('colour')->nullable();
            $table->decimal('weight', 8, 2)->nullable(); // trọng lương kg
            $table->decimal('volume', 8, 2)->nullable(); // Thể tích cm3
            $table->string('unit')->nullable();
            $table->text('note')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedInteger('quantity_in_progress')->default(0);
            $table->unsignedInteger('status')->default(0);
            $table->decimal('price_cny', 15, 2)->nullable();
            $table->string('discount', 10, 2)->nullable()->default(0);
            $table->decimal('fee_inland_ship', 10, 2)->nullable()->default(0);
            $table->unsignedInteger('customer_order_id')->nullable();
            $table->unsignedInteger('shop_id')->nullable();
            $table->timestamps();

            $table->foreign('customer_order_id')->references('id')->on('customer_orders')->onDelete('cascade');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_order_items');
    }
}
