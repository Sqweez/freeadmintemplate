<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('author_id');
            $table->unsignedInteger('user_id')->default(null);
            $table->unsignedInteger('store_id');
            $table->date('date_start');
            $table->date('date_finish');
            $table->boolean('is_completion_required')->default(false);
            $table->boolean('is_completed')->default(false);
            $table->text('text');
            $table->string('title');
            $table->text('assets')->nullable()->default(null);
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
        Schema::dropIfExists('tasks');
    }
}
