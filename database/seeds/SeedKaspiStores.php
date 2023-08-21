<?php

use App\v2\Models\KaspiEntityStore;
use Illuminate\Database\Seeder;

class SeedKaspiStores extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $stores = [
            [
                'id' => 1,
                'pp_id' => 1,
            ],
            [
                'id' => 2,
                'pp_id' => 2,
            ],
            [
                'id' => 3,
                'pp_id' => 3,
            ],
            [
                'id' => 4,
                'pp_id' => 4,
            ],
            [
                'id' => 5,
                'pp_id' => 5,
            ],
            [
                'id' => 14,
                'pp_id' => 6
            ],
            [
                'id' => 16,
                'pp_id' => 7
            ],
            [
                'id' => 19,
                'pp_id' => 9
            ],
            [
                'id' => 23,
                'pp_id' => 10
            ],
        ];

        foreach ($stores as $store) {
            KaspiEntityStore::create([
                'kaspi_entity_id' => __hardcoded(1),
                'store_id' => $store['id'],
                'kaspi_store_id' => $store['pp_id']
            ]);
        }
    }
}
