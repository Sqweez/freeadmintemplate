<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanionTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companion_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('transaction_sum')->default(0);
            $table->unsignedSmallInteger('companion_id');
            $table->unsignedSmallInteger('user_id');
            $table->integer('companion_sale_id')->default(-1);
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
        Schema::dropIfExists('companion_transactions');
    }
}
