<?php

use Illuminate\Database\Seeder;

class QuantitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\ProductBatch::class, 5000)->create();
    }
}