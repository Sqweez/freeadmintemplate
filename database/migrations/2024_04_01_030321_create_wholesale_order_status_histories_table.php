<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWholesaleOrderStatusHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wholesale_order_status_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('wholesale_order_id');
            $table->foreign('wholesale_order_id')
                ->references('id')
                ->on('wholesale_orders')
                ->onDelete('cascade');
            $table->unsignedBigInteger('wholesale_status_id');
            $table->foreign('wholesale_status_id')
                ->references('id')
                ->on('wholesale_order_statuses')
                ->onDelete('cascade');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamp('changed_at')->useCurrent();
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
        Schema::dropIfExists('wholesale_order_status_histories');
    }
}
