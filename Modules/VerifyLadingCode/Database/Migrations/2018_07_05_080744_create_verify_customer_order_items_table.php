<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVerifyCustomerOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('verify_customer_order_items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('verify_lading_code_id')->nullable();
            $table->unsignedInteger('customer_order_item_id')->nullable();
            $table->unsignedInteger('quantity_verify')->nullable();
            $table->unsignedTinyInteger('is_broken_gash')->nullable();
            $table->unsignedTinyInteger('is_error_size')->nullable();
            $table->unsignedTinyInteger('is_error_color')->nullable();
            $table->unsignedTinyInteger('is_error_product')->nullable();
            $table->unsignedTinyInteger('is_exuberancy')->nullable();
            $table->unsignedTinyInteger('is_inadequate')->nullable();
            $table->string('image1')->nullable();
            $table->string('image2')->nullable();
            $table->string('image3')->nullable();
            $table->string('image4')->nullable();
            $table->string('image5')->nullable();
            $table->string('note')->nullable();
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
        Schema::dropIfExists('verify_customer_order_items');
    }
}
