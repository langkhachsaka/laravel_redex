<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteUnusedColumnInCustomerOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_order_items', function (Blueprint $table) {
            //$table->dropColumn('fee_inland_ship_China');
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
           $table->double('fee_inland_ship_China', 15, 2)->nullable();

        });
    }
}
