<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransferBatchesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('transfer_batches', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('transfer_id');
            $table->integer('batch_id');
            $table->integer('product_id');
            $table->boolean('is_transferred')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('transfer_batches');
    }
}
