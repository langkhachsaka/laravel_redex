<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLadingCodeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lading_codes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('ladingcodetable_id')->nullable();
            $table->string('ladingcodetable_type')->nullable();
            $table->string('code');
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
        Schema::dropIfExists('lading_codes');
    }
}
