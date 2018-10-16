<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePayInformation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_informations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('transaction_id');
            $table->tinyInteger('type');
            $table->text('data');
            $table->integer('order_id')->nullable();
            $table->timestamps();

            $table->foreign('transaction_id')
                ->references('id')->on('transactions')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('');
    }
}
