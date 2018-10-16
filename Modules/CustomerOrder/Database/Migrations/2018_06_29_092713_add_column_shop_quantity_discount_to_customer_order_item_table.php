<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnShopQuantityDiscountToCustomerOrderItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_order_items', function (Blueprint $table) {
            $table->integer('shop_quantity')->nullable();
            $table->dropColumn('discount');
            $table->decimal('discount_percent', 15, 2)->nullable();
            $table->decimal('discount_price', 15, 2)->nullable();
            $table->integer('discount_formality')->nullable(); // hình thức chiết khấu (1: trước | 2: sau)
            $table->decimal('discount_customer_percent', 15, 2)->nullable();
            $table->decimal('discount_customer_price', 15, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_order_items', function (Blueprint $table) {
            $table->dropColumn('shop_quantity');
            $table->string('discount', 10, 2)->nullable();
            $table->dropColumn('discount_percent');
            $table->dropColumn('discount_price');
            $table->dropColumn('discount_formality');
            $table->dropColumn('discount_customer_percent');
            $table->dropColumn('discount_customer_price');
        });
    }
}
