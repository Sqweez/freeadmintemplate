<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableThumable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('thumbable', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('thumb_id');
            $table->unsignedBigInteger('thumbable_id');
            $table->string('thumbable_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('thumbable');
    }
}
