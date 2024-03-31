<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWholesaleOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wholesale_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('wholesale_client_id');
            $table->foreign('wholesale_client_id')
                ->references('id')
                ->on('wholesale_clients');
            $table->string('phone');
            $table->string('email');
            $table->string('name');
            $table->unsignedInteger('payment_type_id');
            $table->unsignedInteger('delivery_type_id');
            $table->unsignedInteger('delivery_price')->nullable();
            $table->text('comment')->nullable();
            $table->boolean('is_paid')->default(false);
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
        Schema::dropIfExists('wholesale_orders');
    }
}
