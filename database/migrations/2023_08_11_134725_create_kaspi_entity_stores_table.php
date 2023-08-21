<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKaspiEntityStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kaspi_entity_stores', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('kaspi_entity_id');
            $table->unsignedInteger('store_id');
            $table->unsignedInteger('kaspi_store_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kaspi_entity_stores');
    }
}
