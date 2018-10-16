<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('username');
            $table->string('password');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->date('birthday')->nullable();
            $table->unsignedTinyInteger('role');
            $table->unsignedInteger('warehouse_id')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('set null');
        });

        $user = [
            [
                'name' => 'Admin',
                'username' => 'admin',
                'phone' => '0123456789',
                'role' => 10,
                'email' => 'admin@email.com',
                'password' => '$2y$10$86Ua846EsOlT4O4RHv.nUOZnHhc058eFoIdQ4kc2Puez.pRW9fhjm', /*123456*/
                'remember_token' => str_random(10),
            ]
        ];
        DB::table('users')->insert($user);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
