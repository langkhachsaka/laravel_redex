<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ImportTableDonViHanhChinhSql extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(file_get_contents(__DIR__ . '/don_vi_hanh_chinh.sql'));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('devvn_quanhuyen');
        Schema::dropIfExists('devvn_tinhthanhpho');
        Schema::dropIfExists('devvn_xaphuongthitran');
    }
}
