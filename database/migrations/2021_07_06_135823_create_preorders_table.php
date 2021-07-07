<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreordersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('preorders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('client_id');
            $table->bigInteger('user_id');
            $table->bigInteger('store_id');
            $table->integer('payment_type');
            $table->integer('status')
                ->comment('0 - в ожидании, 1 - подтверждено, -1 - отменено');
            $table->text('comment')->nullable()->default(null);
            $table->integer('amount');
            $table->integer('sale_id')->default(null);
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
        Schema::dropIfExists('preorders');
    }
}
