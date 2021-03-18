<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableImagable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('imagable', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('image_id');
            $table->unsignedBigInteger('imagable_id');
            $table->string('imagable_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('imagable');
    }
}
