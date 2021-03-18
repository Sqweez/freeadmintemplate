<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableCerfiticates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('barcode')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->integer('amount')->default(0);
            $table->date('expired_at')->default(null);
            $table->boolean('active')->default(true);
            $table->unsignedBigInteger('used_sale_id')->default(null);
            $table->unsignedBigInteger('sale_id')->default(null);
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
        Schema::dropIfExists('certificates');
    }
}
