<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableKaspiEntitesKaspiToken extends Migration
{
    public function up(): void
    {
        Schema::table('kaspi_entities', function (Blueprint $table) {
            $table->string('kaspi_token')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('kaspi_entities', function (Blueprint $table) {
            $table->dropColumn('kaspi_token');
        });
    }
}
