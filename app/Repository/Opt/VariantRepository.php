<?php

namespace App\Repository\Opt;

use App\Store;
use App\v2\Models\Product;
use App\v2\Models\ProductSku;
use App\v2\Models\WholesaleClient;

class VariantRepository
{
    public function get(Product $product, ?WholesaleClient $client): \Illuminate\Support\Collection
    {

        if (!$client) {
            return collect([]);
        }

        $productSkus = $product
            ->sku()
            ->with(['attributes'])
            ->with(['batches' => function ($query) {
                return $query->where('store_id', Store::whereTypeId(4)->pluck('id'))->select(['id', 'product_id', 'store_id', 'quantity']);
            }])
            ->whereHas('batches', function ($query) {
                return $query->where('store_id', Store::whereTypeId(4)->pluck('id'))->where('quantity', '>', 0);
            })
            ->get();

        $cartItems = $client->cart->items();
        return $productSkus
            ->map(function (ProductSku $productSku) use ($cartItems) {
                $needle = $cartItems->where('product_id', $productSku->id)->sum('count');
                $productSku['quantity'] = $productSku->batches->sum('quantity') - $needle;
                return $productSku;
            })
            ->filter(function ($value) {
                return $value['quantity'] > 0;
            })
            ->values();
    }
}
