<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableFooter extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('footer', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('addresses')->nullable();
            $table->text('contacts')->nullable();
        });

        \App\v2\Models\Footer::create([
            'addresses' => [
                [
                    'city' => 'Павлодар',
                    'address' => 'САТПАЕВА 174, БУТИК 4 (ЗДАНИЕ ЗОЛОТОГО ТЕЛЁНКА)'
                ],
                [
                    'city' => 'Караганда',
                    'address' => 'БУХАР ЖЫРАУ, 41'
                ],
                [
                    'city' => 'ЭКИБАСТУЗ',
                    'address' => 'МАШХУР ЖУСУП, 29'
                ],
                [
                    'city' => 'УСТЬ-КАМЕНОГОРСК',
                    'address' => 'УЛ. НАЗАРБАЕВА, 52'
                ],
                [
                    'city' => 'СЕМЕЙ',
                    'address' => 'ПРОСПЕКТ ШАКАРИМА 60, ТД "ТРИУМФ"'
                ],
            ],
            'contacts' => [
                [
                    'city' => 'Семей',
                    'phone' => '+77473534363',
                    'name' => 'Диас'
                ],
                [
                    'city' => 'ПАВЛОДАР',
                    'phone' => '+77053000277',
                    'name' => 'АНДРЕЙ'
                ],
                [
                    'city' => 'ЭКИБАСТУЗ',
                    'phone' => '+77770873576',
                    'name' => 'Фариз'
                ],
                [
                    'city' => 'ЭКИБАСТУЗ',
                    'phone' => '+77771575318',
                    'name' => 'Виктория'
                ],
                [
                    'city' => 'УСТЬ-КАМЕНОГОРСК',
                    'phone' => '+77779961646',
                    'name' => 'АНВАР'
                ],
                [
                    'city' => 'КАРАГАНДА',
                    'phone' => '+77775705356',
                    'name' => 'АНУАР'
                ],
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('footer');
    }
}
