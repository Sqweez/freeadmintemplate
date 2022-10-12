<?php

namespace App\Actions\Reports;

use App\Sale;

class GetProductSalesRating {

    public function handle($start, $finish, $store_id = -1) {
        return Sale::query()
            ->when($store_id !== -1, function ($query) use ($store_id) {
                return $query->where('store_id', $store_id);
            })
            ->whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $finish)
            ->with('products.product.product.attributes')
            ->with('products.product.product.category')
            ->with('products.product.product.manufacturer')
            //->with('products.product.product.attributes')
            ->get()
            ->map(function ($sale) {
                $sale['products'] = $sale->products->map(function ($product) {
                    $product->append('final_price');
                    $product['main_product_id'] = $product->product->product_id;
                    return $product;
                });
                return $sale;
            })
            ->pluck('products')
            ->values()
            ->flatten()
            ->groupBy('main_product_id')
            ->map(function ($products, $product_id) {

                $product = $products->first()->product->product;

                $product_name = trim(sprintf("%s | %s | %s | %s",
                    $product->product_name,
                    $product->manufacturer->manufacturer_name,
                    $product->category->category_name,
                    optional($product->attributes->first())->attribute_value ?? '',
                ));

                return [
                    'product_id' => $product_id,
                    'total' => collect($products)->reduce(function ($a, $c) {
                        return $a + $c['final_price'];
                    }, 0),
                    'product_name' => $product_name,
                    'link' => sprintf('https://iron-addicts.kz/product/%s/%s',
                        \Str::slug($product->product_name), $product_id
                    )
                ];
            })
            ->values()
            ->sortByDesc('total')
            ->take(10)
            ->values();
    }

}
