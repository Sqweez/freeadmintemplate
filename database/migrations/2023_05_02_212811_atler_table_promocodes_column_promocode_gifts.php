<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AtlerTablePromocodesColumnPromocodeGifts extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('promocodes', function (Blueprint $table) {
            $table->text('promocode_gifts')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('promocodes', function (Blueprint $table) {
            $table->dropColumn('promocode_gifts');
        });
    }
}
