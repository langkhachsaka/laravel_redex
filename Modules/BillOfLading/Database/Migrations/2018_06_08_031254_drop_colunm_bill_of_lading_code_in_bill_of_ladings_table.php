<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropColunmBillOfLadingCodeInBillOfLadingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bill_of_ladings', function (Blueprint $table) {
            $table->dropColumn('bill_of_lading_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bill_of_ladings', function (Blueprint $table) {
            $table->string('bill_of_lading_code')->nullable();
        });
    }
}
