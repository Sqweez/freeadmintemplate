<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableV2RevisionProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('v2_revision_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('revision_id');
            $table->foreign('revision_id')
                ->references('id')
                ->on('v2_revisions');
            $table->unsignedBigInteger('product_sku_id');
            $table->foreign('product_sku_id')
                ->references('id')
                ->on('product_sku');
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')
                ->references('id')
                ->on('products');
            $table->unsignedInteger('count_expected')->nullable();
            $table->unsignedInteger('count_actual')->nullable();
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
        Schema::dropIfExists('v2_revision_products');
    }
}
