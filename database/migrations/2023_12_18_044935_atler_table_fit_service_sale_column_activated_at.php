<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AtlerTableFitServiceSaleColumnActivatedAt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fit_service_sales', function (Blueprint $table) {
            $table->dateTime('activated_at')->nullable();
            $table->date('valid_until')->nullable();
            $table->integer('validity_in_days')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fit_service_sales', function (Blueprint $table) {
            $table->dropColumn('activated_at');
            $table->dropColumn('valid_until');
            $table->dropColumn('validity_in_days');
        });
    }
}
