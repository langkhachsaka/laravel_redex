<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddShippingColumnsToCustomerOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_orders', function (Blueprint $table) {
            $table->string('customer_shipping_provincial_id')->nullable();
            $table->string('customer_shipping_district_id')->nullable();
            $table->string('customer_shipping_ward_id')->nullable();
            $table->unsignedInteger('customer_shipping_address_id')->nullable();
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
            $table->dropColumn('customer_shipping_provincial_id');
            $table->dropColumn('customer_shipping_district_id');
            $table->dropColumn('customer_shipping_ward_id');
            $table->dropColumn('customer_shipping_address_id');
        });
    }
}
