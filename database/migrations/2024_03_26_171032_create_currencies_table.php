<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurrenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('code');
            $table->string('unicode_symbol');
        });

        DB::table('currencies')->insert([
            'id' => 1,
            'name' => 'Тенге',
            'code' => 'KZT',
            'unicode_symbol' => '₸',
        ]);
        DB::table('currencies')->insert([
            'id' => 2,
            'name' => 'Рубль',
            'code' => 'RUB',
            'unicode_symbol' => '₽',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('currencies');
    }
}
