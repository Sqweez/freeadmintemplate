<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableWholesaleClientsColumnsPersonalData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wholesale_clients', function (Blueprint $table) {
            $table->boolean('has_russian_passport')->default(true);
            $table->unsignedInteger('legal_type_id')->default(1);
            $table->text('passport')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wholesale_clients', function (Blueprint $table) {
            $table->dropColumn(['has_russian_passport', 'legal_type_id', 'passport']);
        });
    }
}
