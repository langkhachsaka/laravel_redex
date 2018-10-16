<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->renameColumn('bill_of_lading_code', 'lading_code');
            $table->unsignedInteger('customer_id')->nullable();

            //$table->foreign('customer_id', 'fk_deliveries_customer_id-customers_id')->references('id')->on('customers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->renameColumn('lading_code', 'bill_of_lading_code');
            $table->dropColumn('customer_id');

            //$table->dropForeign('fk_deliveries_customer_id-customers_id');
        });
    }
}
