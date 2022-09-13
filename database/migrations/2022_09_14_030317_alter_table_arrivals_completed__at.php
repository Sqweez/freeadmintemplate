<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableArrivalsCompletedAt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('arrivals', function (Blueprint $table) {
            $table->dateTime('completed_at')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->unsignedInteger('completed_id')->nullable();
            $table->unsignedInteger('cancelled_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('arrivals', function (Blueprint $table) {
            $table->dropColumn(['completed_at', 'completed_id', 'cancelled_at', 'cancelled_id']);
        });
    }
}
