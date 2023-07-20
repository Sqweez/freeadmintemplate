<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLegalEntitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('legal_entities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('iin');
            $table->text('address');
            $table->text('iik')->nullable();
            $table->string('bik')->nullable();
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
        Schema::dropIfExists('legal_entities');
    }
}
