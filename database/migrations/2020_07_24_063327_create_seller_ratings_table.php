<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellerRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seller_ratings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('seller_id');
            $table->integer('criteria_id');
            $table->integer('rating');
            $table->string('user_token');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seller_ratings');
    }
}
