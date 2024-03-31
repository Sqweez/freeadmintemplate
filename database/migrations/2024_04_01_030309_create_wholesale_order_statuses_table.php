<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWholesaleOrderStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wholesale_order_statuses', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        DB::table('wholesale_order_statuses')
            ->insert([
                ['name' => 'создан', 'description' => 'Заказ создан'],
                ['name' => 'принят', 'description' => 'Заказ принят в обработку'],
                ['name' => 'собран', 'description' => 'Заказ собран'],
                ['name' => 'отправлен', 'description' => 'Заказ отправлен'],
                ['name' => 'получен', 'description' => 'Заказ получен покупателем'],
                ['name' => 'отменен', 'description' => 'Заказ отменен'],
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wholesale_order_statuses');
    }
}
