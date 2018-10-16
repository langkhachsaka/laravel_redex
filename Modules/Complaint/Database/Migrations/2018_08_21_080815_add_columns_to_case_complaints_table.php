<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToCaseComplaintsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('case_complaints', function (Blueprint $table) {
            $table->string('redex_comment')->nullable();
            $table->integer('redex_solution')->nullable();
            $table->integer('order_office_solution')->nullable();
            $table->unsignedInteger('money_shop_return')->nullable();
            $table->date('date_return_money')->nullable();
            $table->string('add_lading_code')->nullable();
            $table->date('date_of_delivery')->nullable();
            $table->decimal('sum_weight_back')->nullable();
            $table->decimal('sum_weight_delivery')->nullable();
            $table->decimal('total_customer_pay')->nullable();
            $table->decimal('ship_inland_fee')->nullable();
            $table->decimal('shop_pay')->nullable();
            $table->decimal('fee_ship_vn_cn')->nullable();
            $table->decimal('redex_support')->nullable();
            $table->string('note')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('case_complaints', function (Blueprint $table) {

        });
    }
}
