<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCaseComplaintsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('case_complaints', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('complaint_id')->nullable();
            $table->unsignedTinyInteger('case')->nullable();
            $table->unsignedTinyInteger('solution')->nullable();
            $table->string('customer_comment')->nullable();
            $table->string('customer_service_comment')->nullable();
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
        Schema::dropIfExists('case_complaints');
    }
}
