<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablePromocodesFewColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('promocodes', function (Blueprint $table) {
            $table->integer('promocode_condition_id')->default(1);
            $table->integer('promocode_purpose_id')->default(1);
            $table->text('promocode_condition_payload')->nullable();
            $table->text('promocode_purpose_payload')->nullable();
            $table->text('promocode_cascade')->nullable();
            $table->integer('discount')->nullable()->change();
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
            $table->dropColumn([
                'promocode_condition_id',
                'promocode_purpose_id',
                'promocode_condition_payload',
                'promocode_purpose_payload',
                'promocode_cascade'
            ]);
        });
    }
}
