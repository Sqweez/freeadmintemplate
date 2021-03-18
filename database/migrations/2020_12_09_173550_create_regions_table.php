<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('regions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 50);
        });

        $regions = [
            ['name' => 'Города республиканского значения'],
            ['name' => 'Актюбинская область'],
            ['name' => 'Карагандинская область'],
            ['name' => 'Жамбыльская область'],
            ['name' => 'Павлодарская область'],
            ['name' => 'Восточно-Казахстанская область'],
            ['name' => 'Атырауская область'],
            ['name' => 'Костанайская область'],
            ['name' => 'Кызылординская область'],
            ['name' => 'Западно-Казахстанская область'],
            ['name' => 'Северо-Казахстанская область'],
            ['name' => 'Мангистауская область'],
            ['name' => 'Туркестанская область'],
            ['name' => 'Акмолинская область'],
            ['name' => 'Алматинская область'],
        ];

        foreach ($regions as $region) {
            \App\v2\Models\Region::create($region);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('regions');
    }
}
