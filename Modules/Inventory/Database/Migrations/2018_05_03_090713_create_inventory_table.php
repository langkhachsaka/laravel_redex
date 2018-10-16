<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date_receiving')->nullable();
            $table->string('invoice_code')->nullable();
            $table->string('bill_of_lading_code')->nullable();
            $table->text('reason')->nullable();
            $table->text('description')->nullable();
            $table->text('note')->nullable();
            $table->unsignedInteger('shop_id')->nullable();
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
        Schema::dropIfExists('inventories');
    }
}
