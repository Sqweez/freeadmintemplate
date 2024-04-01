<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AtlerTableWholesaleDocuments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wholesale_orders', function (Blueprint $table) {
            $table->text('waybill')->nullable();
            $table->text('invoice')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wholesale_orders', function (Blueprint $table) {
            $table->dropColumn([
                'waybill',
                'invoice'
            ]);
        });
    }
}
