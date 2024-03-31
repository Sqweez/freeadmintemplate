<?php

namespace App\Repository;

use App\ProductBatch;
use App\Store;
use Illuminate\Support\Collection;

class ProductBatchRepository
{
    public function getProductTotalQuantities(array $productIds, ?Store $store): Collection
    {
        return ProductBatch::query()
            ->when($store, function ($query) use ($store) {
                return $query->where('store_id', $store->id);
            })
            ->selectRaw('product_id, CAST(SUM(quantity) as SIGNED) as total_quantity')
            ->whereIn('product_id', $productIds)
            ->where('quantity', '>', 0)
            ->groupBy('product_id')
            ->get();
    }

    public function getProductQuantityInStore($productId, ?Store $store)
    {
        return ProductBatch::query()
            ->when($store, function ($query) use ($store) {
                return $query->where('store_id', $store->id);
            })
            ->where('product_id', $productId)
            ->selectRaw('product_id, CAST(SUM(quantity) as SIGNED) as total_quantity')
            ->where('quantity', '>', 0)
            ->get()
            ->sum('total_quantity');
    }
}
