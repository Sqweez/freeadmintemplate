<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWholesaleOrderProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wholesale_order_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('wholesale_order_id');
            $table->foreign('wholesale_order_id')
                ->references('id')
                ->on('wholesale_orders');
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')
                ->references('id')
                ->on('product_sku');
            $table->unsignedInteger('price');
            $table->unsignedBigInteger('currency_id');
            $table->foreign('currency_id')
                ->references('id')
                ->on('currencies');
            $table->unsignedInteger('purchase_price');
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
        Schema::dropIfExists('wholesale_order_products');
    }
}
