<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterArrivalsColumnCommentArrivedAt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('arrivals', function (Blueprint $table) {
            $table->string('comment')->default('');
            $table->date('arrived_at')->default(null)->nullable();
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
            $table->dropColumn('comment');
            $table->dropColumn('arrived_at');
        });
    }
}
