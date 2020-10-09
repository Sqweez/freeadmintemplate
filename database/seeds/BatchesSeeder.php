<?php

use Illuminate\Database\Seeder;

class BatchesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\ProductBatch::class, 60000)->create();
    }
}
