<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableTaggables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taggables', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('tag_id');
            $table->integer('taggable_id');
            $table->string('taggable_type');
            $table->index('tag_id');
            $table->index('taggable_id');
            $table->index('taggable_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('taggables', function (Blueprint $table) {
            $table->dropIfExists();
        });
    }
}
