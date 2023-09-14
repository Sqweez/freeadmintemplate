<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablePromocodesColumnPromocodeApplyTypeId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('promocodes', function (Blueprint $table) {
            $table->string('title')->nullable();
            $table->unsignedInteger('promocode_apply_type_id')->default(1)->after('discount');
            $table->text('available_stores')->nullable();
            $table->unsignedInteger('total_use_quantity')->nullable();
            $table->unsignedInteger('per_client_use_quantity')->nullable();
            $table->unsignedInteger('apply_to_clients_id')->default(1);
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
            $table->dropColumn('title');
            $table->dropColumn('promocode_apply_type_id');
            $table->dropColumn('available_stores');
            $table->dropColumn('per_client_use_quantity');
            $table->dropColumn('total_use_quantity');
            $table->dropColumn('apply_to_clients_id');
        });
    }
}
