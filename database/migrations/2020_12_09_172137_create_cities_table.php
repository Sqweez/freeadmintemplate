<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 50);
            $table->unsignedInteger('region_id')->default(null);
        });

        $cities = [
            ['name' => 'Алматы', 'region_id' => 1],
            ['name' => 'Нур-Султан', 'region_id' => 1],
            ['name' => 'Шымкент', 'region_id' => 1],
            ['name' => 'Актобе', 'region_id' => 2],
            ['name' => 'Караганда', 'region_id' => 3],
            ['name' => 'Рудный', 'region_id' => 3],
            ['name' => 'Темиртау', 'region_id' => 3],
            ['name' => 'Тараз', 'region_id' => 4],
            ['name' => 'Павлодар', 'region_id' => 5],
            ['name' => 'Экибастуз', 'region_id' => 5],
            ['name' => 'Аксу', 'region_id' => 5],
            ['name' => 'Усть-Каменогорск', 'region_id' => 6],
            ['name' => 'Семей', 'region_id' => 6],
            ['name' => 'Атырау', 'region_id' => 7],
            ['name' => 'Костанай', 'region_id' => 8],
            ['name' => 'Кызылорда', 'region_id' => 9],
            ['name' => 'Уральск', 'region_id' => 10],
            ['name' => 'Петропавловск', 'region_id' => 11],
            ['name' => 'Актау', 'region_id' => 12],
            ['name' => 'Туркестан', 'region_id' => 13],
            ['name' => 'Кокшетау', 'region_id' => 14],
            ['name' => 'Талдыкорган', 'region_id' => 15],
        ];

        foreach ($cities as $city) {
            \App\v2\Models\City::create($city);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cities');
    }
}
