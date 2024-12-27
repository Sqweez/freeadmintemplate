<?php

namespace App\Repository;

use App\v2\Models\KaspiEntityStore;
use App\v2\Models\ProductSku;
use Illuminate\Support\Collection;

class ProductRepository
{
    public function getVisibleProductsForKaspiEntity($kaspiEntityId): array
    {
        $products = ProductSku::query()
            ->whereHas('product', function ($q) use ($kaspiEntityId) {
                return $q->whereHas('kaspi_price', function ($sQ) use ($kaspiEntityId) {
                    return $sQ
                        ->where('is_visible', true)
                        ->where('price', '>', 1)
                        ->where('kaspi_entity_id', $kaspiEntityId);
                });
            })
            ->with(['attributes', 'attributes.attribute_name'])
            ->with(['product', 'product.attributes', 'product.attributes.attribute_name'])
            ->with(['product.kaspi_price' => function ($q) use ($kaspiEntityId) {
                return $q->where('kaspi_entity_id', $kaspiEntityId);
            }])
            ->with('product.manufacturer')
            ->with('product.product_images')
            ->with(['batches' => function ($q) {
                return $q->where('quantity', '>', 0);
            }])
            ->get()
            ->sortBy('product_id')
            ->values()
            ->map(function ($product) {
                $product['batches'] = collect($product['batches'])->map(function ($_product) {
                    return $_product;
                });
                return $product;
            });

        $stores = KaspiEntityStore::whereKaspiEntityId($kaspiEntityId)->get();
        return $products->map(function ($product) use ($stores) {
            return [
                'sku' => $product['id'],
                'product_name' => $product['manufacturer']['manufacturer_name'] . ' ' . $product['product_name'] . ' ' . collect($product['attributes'])->pluck('attribute_value')->join(' ') . ' ' . collect($product['product']['attributes'])->pluck('attribute_value')->join(' '),
                'brand' => $product['manufacturer']['manufacturer_name'],
                'base_name' => $product['product_name'],
                'price' => $product['product']['kaspi_price'][0]['price'],//['kaspi_produce_price'],
                'category_id' => $product['product']['category_id'],
                'attributes' => collect($product['attributes'])->mergeRecursive($product['product']['attributes']),
                'images' => $product['product']['product_images'],
                'availabilities' => collect($stores)->map(function ($store) use ($product) {
                    return [
                        'available' => collect($product['batches'])->filter(function ($item) use ($store) {
                            return $item['store_id'] === $store['store_id'];
                        })->sum('quantity')
                        'storeId' => 'PP' . ($store['kaspi_store_id'])];
                }),
                'quantities' => collect($stores)->map(function ($store) use ($product) {
                    return [
                        'store_id' => $store->id,
                        'quantity' => collect($product['batches'])->filter(function ($item) use ($store) {
                            return $item['store_id'] === $store['store_id'];
                        })->count()
                    ];
                })];
        })->toArray();
    }

    public function getFullAttributes(ProductSku $sku): Collection
    {
        if (!$sku->relationLoaded('attributes')) {
            $sku->load('attributes');
        }
        if (!$sku->relationLoaded('product.attributes')) {
            $sku->load('product.attributes');
        }
        return collect($sku->attributes ?? [])
            ->merge(
                collect($sku->product->attributes ?? [])
            )
            ->values();
    }

    public function getFullAttributeValues(ProductSku $sku): Collection
    {
        return $this->getFullAttributes($sku)
            ->pluck('attribute_value');
    }

    public function getProducts($payload = [])
    {
        return ProductSku::with(ProductSku::PRODUCT_SKU_WITH_ADMIN_LIST)
            ->when(isset($payload['iherb']), function ($query) {
                return $query->whereHas('product', function ($subQuery) {
                    return $subQuery->where('is_iherb', true);
                });
            })
            ->when(isset($payload['only_opt']), function ($query) {
                return $query->whereHas('product', function ($subQuery) {
                    return $subQuery->where('is_opt', true);
                })->with('product.wholesale_prices.currency');
            })
            ->when(!isset($payload['only_opt']), function ($query) {
                return $query->whereHas('product', function ($subQuery) {
                    return $subQuery->where('is_opt', false);
                });
            })
            ->when(isset($payload['only_main']), function ($query) {
                return $query->groupBy('product_id')->without('attributes');
            })
            ->when(isset($payload['per_page']), function ($q) {
                return $q->limit(10);
            })
            ->orderBy('product_id')
            ->orderBy('id')
            ->get()
            ->sortBy('product_name');
    }

    public function getById($id)
    {
        $sku = ProductSku::withCount('relativeSku')->whereKey($id)->first();
        $sku->load('product.kaspi_price');
        $sku->load('product.filters.attribute_name');
        $sku->load('product.wholesale_prices.currency');
        return $sku;
    }
}
