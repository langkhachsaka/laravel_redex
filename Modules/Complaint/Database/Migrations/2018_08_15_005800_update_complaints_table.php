<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateComplaintsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropColumn('ordertable_id')->nullable();
            $table->dropColumn('ordertable_type')->nullable();
            $table->dropColumn('title')->nullable();
            $table->dropColumn('content')->nullable();
            $table->dropColumn('note')->nullable();
            $table->dropColumn('date_end_expected')->nullable();
            $table->dropColumn('solution')->nullable();
            $table->dropColumn('file_report_path')->nullable();
            $table->dropColumn('user_id')->nullable();
            $table->dropColumn('customer_id')->nullable();
            $table->string('lading_code')->nullable();
            $table->unsignedTinyInteger('error_size')->nullable();
            $table->unsignedTinyInteger('error_collor')->nullable();
            $table->unsignedTinyInteger('error_product')->nullable();
            $table->unsignedTinyInteger('inadequate_product')->nullable();
            $table->text('comment_error_size')->nullable();
            $table->text('comment_error_collor')->nullable();
            $table->text('comment_error_product')->nullable();
            $table->text('comment_inadequate_product')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('', function (Blueprint $table) {

        });
    }
}
