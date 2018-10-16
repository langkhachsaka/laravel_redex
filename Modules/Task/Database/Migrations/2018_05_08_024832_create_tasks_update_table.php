<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksUpdateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks_updates', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('task_id');
            $table->unsignedInteger('customer_order_id')->nullable();
            $table->unsignedTinyInteger('task_type')->nullable();
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->unsignedTinyInteger('status')->nullable();
            $table->unsignedInteger('creator_id')->nullable();
            $table->unsignedInteger('performer_id')->nullable();
            $table->unsignedInteger('updater_id')->nullable();
            $table->unsignedInteger('complaint_id')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->string('comment')->nullable();
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
        Schema::dropIfExists('tasks_updates');
    }
}
