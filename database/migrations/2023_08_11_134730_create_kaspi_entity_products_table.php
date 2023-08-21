<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKaspiEntityProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kaspi_entity_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('kaspi_entity_id');
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('price');
            $table->boolean('is_visible')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kaspi_entity_products');
    }
}
