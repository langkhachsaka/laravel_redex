<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillOfLadingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bill_of_ladings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('bill_of_lading_code')->nullable();
            $table->text('file_path')->nullable();
            $table->unsignedInteger('customer_id')->nullable();
            $table->string('customer_billing_name')->nullable();
            $table->text('customer_billing_address')->nullable();
            $table->string('customer_billing_phone')->nullable();
            $table->string('customer_shipping_name')->nullable();
            $table->text('customer_shipping_address')->nullable();
            $table->string('customer_shipping_phone')->nullable();
            $table->unsignedInteger('courier_company_id')->nullable();
            $table->unsignedInteger('seller_id')->nullable();
            $table->unsignedTinyInteger('status')->nullable();
            $table->dateTime('end_date')->nullable();

            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('courier_company_id')
                ->references('id')
                ->on('courier_companies')
                ->onDelete('set null');
            $table->foreign('seller_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bill_of_ladings');
    }
}
