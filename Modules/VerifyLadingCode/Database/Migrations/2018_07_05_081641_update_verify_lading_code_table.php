<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateVerifyLadingCodeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('verify_lading_codes', function (Blueprint $table) {
            $table->unsignedTinyInteger('status')->nullable();
            $table->dropColumn(['is_gash_stamp','is_broken_gash','is_error_size', 'is_error_color', 'is_error_product','is_exuberancy','is_inadequate'
                ,'height','width','length','weight','image1','image2','image3','image4','image5','note']);
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
