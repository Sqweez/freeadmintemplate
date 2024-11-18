<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableKaspiEntriesColumnsAddress extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('kaspi_entity_stores', function (Blueprint $table) {
            $table->string('kaspi_external_id');
            $table->json('address');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('kaspi_entity_stores', function (Blueprint $table) {
            $table->dropColumn('kaspi_external_id');
            $table->dropColumn('address');
        });
    }
}
