<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOptDailyDealProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('opt_daily_deal_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('opt_daily_deal_id');
            $table
                ->foreign('opt_daily_deal_id')
                ->references('id')
                ->on('opt_daily_deals')
                ->onDelete('cascade');
            $table->unsignedBigInteger('product_id');
            $table
                ->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');
            $table->unsignedInteger('discount');
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
        Schema::dropIfExists('opt_daily_deal_products');
    }
}
