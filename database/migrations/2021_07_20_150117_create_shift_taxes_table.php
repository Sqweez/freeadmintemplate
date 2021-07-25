<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShiftTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shift_taxes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('shift_tax')->comment('Стоимость смены');
            $table->integer('sale_percent')
                ->comment('Процент от продаж')
                ->default(0);
            $table->bigInteger('store_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shift_taxes');
    }
}
