<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFitUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fit_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('gym_id');
            $table->string('name');
            $table->string('login');
            $table->string('fit_role_id');
            $table->string('password');
            $table->string('token')->nullable();
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
        Schema::dropIfExists('fit_users');
    }
}
