<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnIntoVerifyLadingsCode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('verify_lading_codes', function (Blueprint $table) {
            $table->decimal('height',15,2)->nullable();
            $table->decimal('width',15,2)->nullable();
            $table->decimal('length',15,2)->nullable();
            $table->decimal('weight',15,2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('', function (Blueprint $table) {

        });
    }
}
