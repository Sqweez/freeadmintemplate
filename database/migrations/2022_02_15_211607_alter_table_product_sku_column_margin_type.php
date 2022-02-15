<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableProductSkuColumnMarginType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_sku', function (Blueprint $table) {
            $table->unsignedInteger('margin_type_id')->default(2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_sku', function (Blueprint $table) {
            $table->dropColumn('margin_type_id');
        });
    }
}
