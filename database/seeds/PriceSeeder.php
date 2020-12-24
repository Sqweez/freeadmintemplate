<?php

use Illuminate\Database\Seeder;

class PriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $prices = DB::table('prices')->get()->map(function ($item) {
            $sku_id = $item->product_id;
            $sku = DB::table('product_sku')->find($sku_id);
            if ($sku) {
                return [
                    'price' => $item->price,
                    'store_id' => $item->store_id,
                    'product_id' => $sku->product_id,
                ];
            } else {
                return null;
            }
        })->filter(function ($i) {return $i !== null;})->unique(function ($item) {
            return $item['product_id'].$item['price'].$item['store_id'];
        })->values()->all();

        DB::table('prices')->insert($prices);
    }
}
