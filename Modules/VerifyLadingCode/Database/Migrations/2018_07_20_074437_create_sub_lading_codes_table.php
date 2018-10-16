<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubLadingCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_lading_codes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('lading_code')->nullable();
            $table->string('sub_lading_code')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('length')->nullable();
            $table->unsignedInteger('weight')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sub_lading_codes');
    }
}
