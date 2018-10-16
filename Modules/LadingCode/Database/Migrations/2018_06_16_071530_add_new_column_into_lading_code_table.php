<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewColumnIntoLadingCodeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lading_codes', function (Blueprint $table) {
            $table->float('height', 8, 2)->nullable();
            $table->float('width', 8, 2)->nullable();
            $table->float('length', 8, 2)->nullable();
            $table->decimal('weight', 8, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lading_codes', function (Blueprint $table) {
            $table->dropColumn('height');
            $table->dropColumn('width');
            $table->dropColumn('length');
            $table->dropColumn('weight');
        });
    }
}
