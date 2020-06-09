<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiteFieldsClients extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('password')->default('');
            $table->string('address')->default('');
            $table->string('user_token', 60)->default('');
            $table->string('city')->default('Павлодар');
            $table->string('email')->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('password');
            $table->dropColumn('address');
            $table->dropColumn('user_token');
            $table->dropColumn('city');
            $table->dropColumn('email');
        });
    }
}
