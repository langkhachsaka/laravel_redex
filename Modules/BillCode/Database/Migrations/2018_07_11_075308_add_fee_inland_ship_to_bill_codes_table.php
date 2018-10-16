<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFeeInlandShipToBillCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bill_codes', function (Blueprint $table) {
            $table->integer('fee_ship_inland')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bill_codes', function (Blueprint $table) {
            $table->dropColumn('fee_ship_inland');
        });
    }
}
