<?php

namespace App\Repository\v2;

use App\v2\Models\ProductSku;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductRepository
{
    public function retrieveProducts(array $filters = []): LengthAwarePaginator
    {
        return ProductSku::with(ProductSku::PRODUCT_SKU_WITH_ADMIN_LIST)
            ->when(data_get($filters, 'search'), function ($query) use ($filters) {
                return $query->where('sku_name', 'LIKE', "%{$filters['search']}%");
            })
            ->when(data_get($filters, 'manufacturer_id'), function ($query) use ($filters) {
                return $query->whereHas('product', function ($query) use ($filters) {
                    return $query->where('manufacturer_id', $filters['manufacturer_id']);
                });
            })
            ->when(data_get($filters, 'category_id'), function ($query) use ($filters) {
                return $query->whereHas('product', function ($query) use ($filters) {
                    return $query->where('category_id', $filters['category_id']);
                });
            })
            ->when(data_get($filters, 'subcategory_id'), function ($query) use ($filters) {
                return $query->whereHas('product', function ($query) use ($filters) {
                    return $query->where('subcategory_id', $filters['subcategory_id']);
                });
            })
            ->with([
                'positive_batches' => function ($query) use ($filters) {
                    if (data_get($filters, 'store_id')) {
                        $query->where('store_id', $filters['store_id']);
                    }
                    $query->select(['id', 'product_id', 'store_id', 'quantity']);
                }
            ])
            ->when(data_get($filters, 'only_in_stock'), function ($query) use ($filters) {
                $query->whereHas('positive_batches', function ($subQuery) use ($filters) {
                    if (data_get($filters, 'store_id')) {
                        $subQuery->where('store_id', $filters['store_id']);
                    }
                });
            })
            ->when(data_get($filters, 'margin_type_id'), function ($query) use ($filters) {
                return $query->where('margin_type_id', $filters['margin_type_id']);
            })
            ->orderBy('sku_name')
            ->paginate(10);
    }
}
