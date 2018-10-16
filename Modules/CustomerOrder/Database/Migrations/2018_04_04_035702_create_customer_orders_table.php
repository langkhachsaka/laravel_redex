<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('status')->default(0);
            $table->unsignedInteger('customer_id')->nullable();
            $table->unsignedInteger('seller_id')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->string('customer_billing_name')->nullable();
            $table->text('customer_billing_address')->nullable();
            $table->string('customer_billing_phone')->nullable();
            $table->string('customer_shipping_name')->nullable();
            $table->text('customer_shipping_address')->nullable();
            $table->string('customer_shipping_phone')->nullable();
            $table->decimal('fee_inland_ship', 15, 2)->nullable();
            $table->decimal('fee_ship_inner_city', 15, 2)->nullable();
            $table->decimal('fee_ship_tq2vn', 15, 2)->nullable();
            $table->decimal('money_debt', 15, 2)->nullable();
            $table->decimal('money_deposit', 15, 2)->nullable();
            $table->decimal('money_exchange_rate', 15, 2)->nullable();
            $table->decimal('total_payment', 15, 2)->nullable();
            $table->decimal('total_price', 15, 2)->nullable();
            $table->decimal('total_price_cny', 15, 2)->nullable();
            $table->decimal('total_price_vn', 15, 2)->nullable();

            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
            $table->foreign('seller_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_orders');
    }
}
