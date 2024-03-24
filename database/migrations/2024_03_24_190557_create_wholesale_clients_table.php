<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWholesaleClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wholesale_clients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('iin', 12);
            $table->string('last_name');
            $table->string('first_name');
            $table->string('patronymic')->nullable();
            $table->unsignedBigInteger('country_id'); // Добавлено поле для страны
            $table->unsignedBigInteger('city_id'); // Добавлено поле для города
            $table->string('phone');
            $table->string('email')->unique();
            $table->string('password');

            // Дополнительные поля
            $table->string('delivery_address')->nullable();
            $table->string('company_type')->nullable();
            $table->boolean('is_active')->default(true);

            // Внешние ключи
            $table->foreign('country_id')->references('id')->on('countries')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->foreign('city_id')->references('id')->on('cities')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            // Типичные поля времени создания и обновления
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wholesale_clients');
    }
}
