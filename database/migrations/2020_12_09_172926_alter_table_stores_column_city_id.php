<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableStoresColumnCityId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->unsignedInteger('city_id');
            $table->index('city_id');
        });

        \App\Store::select(['id', 'city'])->get()->each(function ($store) {
            $city_id = \App\v2\Models\City::whereName($store['city'])->first()->id;
            \App\Store::find($store['id'])->update(['city_id' => $city_id]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn('city_id');
            //$table->dropIndex('city_id');
        });
    }
}
