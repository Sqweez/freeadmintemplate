<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableBaseProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('products', 'products_old');
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('product_name');
            $table->text('product_description')->nullable();
            $table->integer('product_price')->default(0);
            $table->integer('product_discount_price')->default(0);
            $table->boolean('is_hit')->default(false);
            $table->boolean('is_site_visible')->default(true);
            $table->unsignedInteger('grouping_attribute_id')->default(1);
            $table->unsignedInteger('category_id');
            $table->unsignedInteger('subcategory_id');
            $table->unsignedInteger('manufacturer_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('base_products');
        Schema::dropIfExists('products');
        Schema::rename('products_old', 'products');
    }
}
