<?php

namespace App\Resolvers\Opt;

use App\Store;
use App\v2\Models\Product;
use App\v2\Models\WholesaleClient;
use Illuminate\Database\Eloquent\Builder;

abstract class BaseProductResolver
{
    public function getWholesaleStoreIds(): array
    {
        return Store::wholesaleStore()->pluck('id')->toArray();
    }

    public function retrieveCurrency(?WholesaleClient $client = null)
    {
        return optional($client)->preferred_currency_id ?? __hardcoded(2);
    }


    public function getBaseProductQuery($currencyId, $wholesaleStoreIds)
    {
        return Product::query()->OptProducts()->with([
                'subcategory',
                'product_thumbs',
                'attributes',
                'batches' => function ($q) use ($wholesaleStoreIds) {
                    $q->whereIn('store_id', $wholesaleStoreIds)->where('quantity', '>', 0);
                },
                'wholesale_prices' => function ($query) use ($currencyId) {
                    $query->where('currency_id', $currencyId)->with('currency');
                },
            ])->whereHas('wholesale_prices', function ($q) use ($currencyId) {
                return $q->where('currency_id', $currencyId);
            })->whereHas('batches', function ($q) use ($wholesaleStoreIds) {
                return $q->where('store_id', $wholesaleStoreIds)->where('quantity', '>', 0);
            });
    }

    public function filterProducts(Builder $builder, array $filters, $currencyId)
    {
        return $builder->when($filters[Product::FILTER_CATEGORIES] ?? null, function ($query) use ($filters) {
                $query->ofCategory($filters[Product::FILTER_CATEGORIES]);
            })->when($filters[Product::FILTER_SUBCATEGORIES] ?? null, function ($query) use ($filters) {
                $query->ofSubcategory($filters[Product::FILTER_SUBCATEGORIES]);
            })->when($filters[Product::FILTER_BRANDS] ?? null, function ($query) use ($filters) {
                $query->ofBrand($filters[Product::FILTER_BRANDS]);
            })->when(
                $filters[Product::FILTER_PRICES] ?? null && count($filters[Product::FILTER_PRICES]) === 2,
                function ($query) use ($filters, $currencyId) {
                    $query->whereHas('wholesale_prices', function ($subQuery) use ($filters, $currencyId) {
                        $subQuery->where('currency_id', $currencyId)->whereBetween(
                            'price',
                            $filters[Product::FILTER_PRICES]
                        );
                    });
                }
            )->when($filters[Product::FILTER_SEARCH] ?? null, function ($query) use ($filters) {
                $query->ofTag($filters[Product::FILTER_SEARCH]);
            })->when($filters[Product::FILTER_FILTERS] ?? null, function ($query) use ($filters) {
                $query->whereHas('filters', function ($q) use ($filters) {
                    $q->whereIn('id', $filters[Product::FILTER_FILTERS]);
                });
            })
            ->when($filters['product_ids'] ?? null, function ($query) use ($filters) {
                $query->whereIn('id', $filters['product_ids']);
        });
    }
}
