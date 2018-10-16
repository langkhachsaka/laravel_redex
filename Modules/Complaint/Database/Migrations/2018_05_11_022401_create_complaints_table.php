<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComplaintsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('ordertable_id')->nullable();
            $table->string('ordertable_type')->nullable();
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->text('note')->nullable();
            $table->unsignedTinyInteger('status')->nullable();
            $table->date('date_end_expected')->nullable();
            $table->unsignedSmallInteger('solution')->nullable();
            $table->string('file_report_path')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('customer_id')->nullable();

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
        Schema::dropIfExists('complaints');
    }
}
