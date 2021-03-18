<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoalPartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goal_parts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('goal_id');
            $table->integer('category_id')->default(null);
            $table->integer('subcategory_id')->nullable();
            $table->string('name')->default('');
            $table->text('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('goal_parts');
    }
}
