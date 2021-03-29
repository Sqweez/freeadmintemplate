<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableCompanionSalesIsConsignment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companion_sales', function (Blueprint $table) {
            $table->boolean('is_consignment')->default(false);
            $table->integer('transfer_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companion_sales', function (Blueprint $table) {
            $table->dropColumn('is_consignment');
            $table->dropColumn('transfer_id');
        });
    }
}
