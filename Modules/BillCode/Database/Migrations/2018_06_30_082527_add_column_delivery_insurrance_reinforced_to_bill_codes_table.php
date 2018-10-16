<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnDeliveryInsurranceReinforcedToBillCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bill_codes', function (Blueprint $table) {
            $table->integer('delivery_type')->nullable();
            $table->integer('insurance_type')->nullable();
            $table->integer('reinforced_type')->nullable();
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
            $table->dropColumn('delivery_type');
            $table->dropColumn('insurance_type');
            $table->dropColumn('reinforced_type');
        });
    }
}
