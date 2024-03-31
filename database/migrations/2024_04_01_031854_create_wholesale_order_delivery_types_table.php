<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWholesaleOrderDeliveryTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wholesale_order_delivery_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->timestamps();
        });


        DB::table('wholesale_order_delivery_types')
            ->insert([
                [
                    'name' => 'Транспортная компания "КИТ"'
                ],
                [
                    'name' => 'Транспортная компания "Энергия"'
                ],
                [
                    'name' => 'Другое'
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
        Schema::dropIfExists('wholesale_order_delivery_types');
    }
}
