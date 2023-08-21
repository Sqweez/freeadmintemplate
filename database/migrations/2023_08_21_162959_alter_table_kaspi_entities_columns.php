<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableKaspiEntitiesColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kaspi_entities', function (Blueprint $table) {
            $table->string('company_name')->nullable();
            $table->string('merchant_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kaspi_entities', function (Blueprint $table) {
            $table->dropColumn(['company_name', 'merchant_id']);
        });
    }
}
