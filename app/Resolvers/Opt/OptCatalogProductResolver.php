<?php

namespace App\Resolvers\Opt;

use App\Category;
use App\Manufacturer;
use App\Store;
use App\Subcategory;
use App\v2\Models\Currency;
use App\v2\Models\Product;
use App\v2\Models\WholesaleClient;

class OptCatalogProductResolver
{

    public function getProductQuery(array $filters, ?WholesaleClient $client)
    {
        $wholesaleStoreIds = Store::wholesaleStore()->pluck('id')->toArray();
        $currencyId = $this->retrieveCurrency($client);
        $query = Product::query()->OptProducts();

        // Применение фильтров
        $query->when($filters[Product::FILTER_CATEGORIES] ?? null, function ($query) use ($filters) {
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
        });

        // Дополнительные фильтры
        $query->when($filters['product_ids'] ?? null, function ($query) use ($filters) {
            $query->whereIn('id', $filters['product_ids']);
        });

        $query->whereHas('wholesale_prices', function ($q) use ($currencyId) {
            return $q->where('currency_id', $currencyId);
        });

        $query->whereHas('batches', function ($q) use ($wholesaleStoreIds) {
            return $q->where('store_id', $wholesaleStoreIds)->where('quantity', '>', 0);
        });

        // Загрузка связей
        $query->with([
            'batches' => function ($q) use ($wholesaleStoreIds) {
                $q->whereIn('store_id', $wholesaleStoreIds)->where('quantity', '>', 0);
            },
            'wholesale_prices' => function ($query) use ($currencyId) {
                $query->where('currency_id', $currencyId)->with('currency');
            },
            'subcategory',
            'product_thumbs',
            'attributes'
        ]);

        return $query;
    }

    public function attachAdditionalEntities($query)
    {
        return $query
            ->with('subcategory')
            ->with('product_thumbs')
            ->with('attributes');
    }

    public function getFilters($query): array
    {
        $items = $query
            ->select(['id', 'manufacturer_id', 'category_id', 'subcategory_id'])
            ->without('batches')
            ->without('product_thumbs')
            ->without('subcategory')
            ->without('attributes')
            ->get();
        $brandFilters = Manufacturer::query()->whereIn(
            'id',
            $items->pluck('manufacturer_id')->unique()->all()
        )->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'attribute_value' => $item->manufacturer_name
            ];
        });
        $categoriesFilters = Category::query()->whereIn(
            'id',
            $items->pluck('category_id')->unique()->all()
        )->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'attribute_value' => $item->category_name
            ];
        });
        $subcategoryFilters = Subcategory::query()->whereIn(
            'id',
            $items->pluck('subcategory_id')->unique()->all()
        )->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'attribute_value' => $item->subcategory_name
            ];
        });

        $priceFilters = $this->getPrices($items);
        $filters = [];
        $filters[] = [
            'attribute_name' => 'Категория',
            'id' => Product::FILTER_CATEGORIES,
            'values' => $categoriesFilters->toArray(),
        ];
        $filters[] = [
            'attribute_name' => 'Подкатегория',
            'id' => Product::FILTER_SUBCATEGORIES,
            'values' => $subcategoryFilters->toArray(),
        ];
        $filters[] = [
            'attribute_name' => 'Бренды',
            'id' => Product::FILTER_BRANDS,
            'values' => $brandFilters->toArray(),
        ];
        $filters = collect($filters)
            ->filter(function ($item) {
                return count($item['values']) > 1;
            })
            ->toArray();
        if ($priceFilters) {
            array_unshift($filters, $priceFilters);
        }
        return $filters;
    }

    private function getPrices($products): ?array
    {
        $prices = $products->flatMap(function ($product) {
            return collect($product['wholesale_prices']);
        });

        $priceMax = $prices->max('price');
        $priceMin = $prices->min('price');

        if ($priceMax === $priceMin) {
            return null;
        }

        return [
            'attribute_name' => 'Цена',
            'id' => 'prices',
            'type' => 'range',
            'min' => $prices->min('price'),
            'max' => $prices->max('price'),
            'currencySign' => Currency::find($prices->first()['currency_id'])->unicode_symbol
        ];
    }

    public function retrieveCurrency(?WholesaleClient $client)
    {
        return optional($client)->preferred_currency_id ?? __hardcoded(2);
    }
}
