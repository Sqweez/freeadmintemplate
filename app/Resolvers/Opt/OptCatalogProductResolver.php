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
        $currencyId = $this->retrieveCurrency($client);
        return Product::query()->OptProducts()->when(
            !empty($filters[Product::FILTER_CATEGORIES]), function ($query) use ($filters) {
            return $query->ofCategory($filters[Product::FILTER_CATEGORIES]);
        }
        )->when(!empty($filters[Product::FILTER_SUBCATEGORIES]), function ($query) use ($filters) {
            return $query->ofSubcategory($filters[Product::FILTER_SUBCATEGORIES]);
        })->when(!empty($filters[Product::FILTER_BRANDS]), function ($query) use ($filters) {
            return $query->ofBrand($filters[Product::FILTER_BRANDS]);
        })->when(!empty($filters[Product::FILTER_PRICES]), function ($query) use ($filters, $currencyId) {
            return $query->whereHas('wholesale_prices', function ($subQuery) use ($filters, $currencyId) {
                \Log::info('prices', $filters[Product::FILTER_PRICES]);
                return $subQuery
                    ->where('currency_id', $currencyId)
                    ->where('price', '>=', $filters[Product::FILTER_PRICES][0])
                    ->where('price', '<=', $filters[Product::FILTER_PRICES][1]);
            });
        })->when(!empty($filters[Product::FILTER_SEARCH]), function ($query) use ($filters) {
            return $query->ofTag($filters[Product::FILTER_SEARCH]);
        })->when(!empty($filters[Product::FILTER_FILTERS]), function ($query) use ($filters) {
            return $query->whereHas('filters', function ($q) use ($filters) {
                return $q->whereIn('id', $filters[Product::FILTER_FILTERS]);
            });
        })
        ->when(isset($filters['product_ids']), function ($query) use ($currencyId, $filters) {
            return $query->whereIn('id', $filters['product_ids']);
        })
        ->when(empty($filters[Product::FILTER_PRICES]), function ($query) use ($currencyId) {
            return $query->whereHas('wholesale_prices', function ($query) use ($currencyId) {
                return $query->where('currency_id', $currencyId);
            });
        })
        ->with(['wholesaleFavorite' => function ($query) use ($client) {
            return $query->where('wholesale_client_id', optional($client)->id);
        }])
        ->with(['sku.batches' => function ($q) {
            $wholesaleStoreId = Store::wholesaleStore()->first()->id;
            return $q
                ->where('store_id', $wholesaleStoreId)
                ->where('quantity', '>', 0);
        }])
        ->whereHas('sku', function ($query) {
            $wholesaleStoreId = Store::wholesaleStore()->first()->id;
            return $query->whereHas('batches', function ($query) use ($wholesaleStoreId) {
                return $query
                    ->where('store_id', $wholesaleStoreId)
                    ->where('quantity', '>', 0);
            });
        })
        ->with([
            'wholesale_prices' => function ($query) use ($currencyId) {
                return $query->where('currency_id', $currencyId);
            }
        ]);
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
        $items = $query->select(['id', 'manufacturer_id', 'category_id', 'subcategory_id']);
        $brandFilters = Manufacturer::query()->whereIn(
            'id',
            $items->pluck('manufacturer_id')
        )->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'attribute_value' => $item->manufacturer_name
            ];
        });
        $categoriesFilters = Category::query()->whereIn(
            'id',
            $items->pluck('category_id')
        )->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'attribute_value' => $item->category_name
            ];
        });
        $subcategoryFilters = Subcategory::query()->whereIn(
            'id',
            $items->pluck('subcategory_id')
        )->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'attribute_value' => $item->subcategory_name
            ];
        });

        $priceFilters = $this->getPrices($query->get());
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
