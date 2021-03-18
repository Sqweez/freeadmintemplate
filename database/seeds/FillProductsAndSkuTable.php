<?php

use Illuminate\Database\Seeder;

class FillProductsAndSkuTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('products')->truncate();
        DB::table('product_sku')->truncate();

        DB::table('products_old')->get()->groupBy('group_id')->each(function ($products, $group_id) {
            $main_product = collect($products)->first();
            $deleted_at = $main_product->deleted_at;
            $product_id = DB::table('products')->insertGetId(
                [
                    'product_name' => $main_product->product_name,
                    'product_description' => $main_product->product_description,
                    'product_price' => $main_product->product_price,
                    'is_hit' => $main_product->is_hit,
                    'is_site_visible' => $main_product->is_site_visible,
                    'deleted_at' => $main_product->deleted_at,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

            collect($products)->each(function ($product) use ($product_id, $deleted_at) {
                DB::table('product_sku')->insert(
                    [
                        'product_id' => $product_id,
                        'id' => $product->id,
                        'product_barcode' => $product->product_barcode,
                        'self_price' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                        'deleted_at' => $deleted_at
                    ]
                );
            });
        });
    }

}
