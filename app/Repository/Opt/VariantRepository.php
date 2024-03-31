<?php

namespace App\Repository\Opt;

use App\Store;
use App\v2\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class VariantRepository
{
    public function get(Product $product): Collection
    {
        return $product
            ->sku()
            ->with(['attributes'])
            ->with(['batches' => function ($query) {
                return $query->where('store_id', Store::whereTypeId(4)->pluck('id'))->select(['id', 'product_id', 'store_id', 'quantity']);
            }])
            ->whereHas('batches', function ($query) {
                return $query->where('store_id', Store::whereTypeId(4)->pluck('id'))->where('quantity', '>', 0);
            })
            ->get();
    }
}
