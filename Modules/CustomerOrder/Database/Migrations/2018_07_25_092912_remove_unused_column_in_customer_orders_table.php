<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveUnusedColumnInCustomerOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_orders', function (Blueprint $table) {
            $table->dropColumn('fee_inland_ship');
            $table->dropColumn('fee_ship_inner_city');
            $table->dropColumn('fee_ship_tq2vn');
            $table->dropColumn('money_debt');
            $table->dropColumn('money_deposit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_orders', function (Blueprint $table) {
            $table->decimal('fee_inland_ship', 15, 2)->nullable();
            $table->decimal('fee_ship_inner_city', 15, 2)->nullable();
            $table->decimal('fee_ship_tq2vn', 15, 2)->nullable();
            $table->decimal('money_debt', 15, 2)->nullable();
            $table->decimal('money_deposit', 15, 2)->nullable();
        });
    }
}
