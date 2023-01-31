<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableDiscountsTableRules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('promocodes', function (Blueprint $table) {
            $table->integer('min_total')->default(0);
            $table->unsignedInteger('brand_id')->nullable();
            $table->text('required_products')->nullable();
            $table->unsignedInteger('free_product_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('promocodes', function (Blueprint $table) {
            $table->dropColumn(['min_total', 'brand_id', 'required_products', 'free_product_id']);
        });
    }
}
