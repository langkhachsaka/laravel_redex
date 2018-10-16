<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('notificationtable_id')->nullable();
            $table->string('notificationtable_type')->nullable();
            $table->text('content')->nullable();
            $table->unsignedInteger('from_user_id')->nullable();
            $table->unsignedInteger('to_user_id')->nullable();
            $table->boolean('is_read')->nullable();

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
        Schema::dropIfExists('notifications');
    }
}
