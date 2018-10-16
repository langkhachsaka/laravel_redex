<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVerifyLadingCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('verify_lading_codes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('lading_code')->nullable();
            $table->unsignedInteger('verifier_id')->nullable();
            $table->unsignedTinyInteger('is_gash_stamp')->nullable();
            $table->unsignedTinyInteger('is_broken_gash')->nullable();
            $table->unsignedTinyInteger('is_error_size')->nullable();
            $table->unsignedTinyInteger('is_error_color')->nullable();
            $table->unsignedTinyInteger('is_error_product')->nullable();
            $table->unsignedTinyInteger('is_exuberancy')->nullable();
            $table->unsignedTinyInteger('is_inadequate')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('length')->nullable();
            $table->unsignedInteger('weight')->nullable();
            $table->string('image1')->nullable();
            $table->string('image2')->nullable();
            $table->string('image3')->nullable();
            $table->string('image4')->nullable();
            $table->string('image5')->nullable();
            $table->string('note')->nullable();
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
        Schema::dropIfExists('verify_lading_codes');
    }
}
