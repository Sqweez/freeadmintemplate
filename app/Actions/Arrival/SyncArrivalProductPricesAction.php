<?php

namespace App\Actions\Arrival;

use App\Product;

class SyncArrivalProductPricesAction {

    public function handle(array $products) {
        collect($products)->each(function ($product) {
            Product::query()
                ->where('product_price', '!=', $product['product_price'])
                ->where('id', $product['base_product_id'])
                ->update(['product_price' => $product['product_price']]);
        });
    }

}
