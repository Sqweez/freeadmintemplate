<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableAttributeValues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attribute_values', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('attribute_id');
            $table->string('attribute_value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attribute_values', function (Blueprint $table) {
            $table->dropIfExists();
        });
    }
}
