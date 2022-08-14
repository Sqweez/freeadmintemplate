<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBestBeforesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('best_befores', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('product_sku_id')->index();
            $table->unsignedInteger('quantity');
            $table->date('best_before');
            $table->unsignedInteger('store_id');
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
        Schema::dropIfExists('best_befores');
    }
}
