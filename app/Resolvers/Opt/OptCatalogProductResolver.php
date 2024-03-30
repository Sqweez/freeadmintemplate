<?php

namespace App\Resolvers\Opt;

use App\Category;
use App\Manufacturer;
use App\Subcategory;
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
        })->when(!empty($filters[Product::FILTER_PRICES]), function ($query) use ($filters) {
            return $query->ofPrice($filters[Product::FILTER_PRICES]);
        })->when(!empty($filters[Product::FILTER_SEARCH]), function ($query) use ($filters) {
            return $query->ofTag($filters[Product::FILTER_SEARCH]);
        })->when(!empty($filters[Product::FILTER_FILTERS]), function ($query) use ($filters) {
            return $query->whereHas('filters', function ($q) use ($filters) {
                return $q->whereIn('id', $filters[Product::FILTER_FILTERS]);
            });
        })
        ->whereHas('wholesale_prices', function ($query) use ($currencyId) {
            return $query->where('currency_id', $currencyId);
        })->with([
            'wholesale_prices' => function ($query) use ($currencyId) {
                return $query->where('currency_id', $currencyId);
            }
        ]);
    }

    public function attachAdditionalEntities($query)
    {
        return $query->with('subcategory')->with('product_thumbs');
    }

    public function getFilters($query): array
    {
        $items = $query->select(['id', 'manufacturer_id', 'category_id', 'subcategory_id']);
        return [
            'brands' => Manufacturer::query()->whereIn(
                'id',
                $items->pluck('manufacturer_id')
            )->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->manufacturer_name
                ];
            }),
            'categories' => Category::query()->whereIn(
                'id',
                $items->pluck('category_id')
            )->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->category_name
                ];
            }),
            'subcategories' => Subcategory::query()->whereIn(
                'id',
                $items->pluck('subcategory_id')
            )->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->subcategory_name
                ];
            }),
            'prices' => $query->get()
        ];
    }

    private function getPrices($products): array
    {
        $maxPrice = $products->flatMap(function ($product) {
            return collect($product['wholesale_prices'])->pluck('price');
        })->max();

        $minPrice = $products->flatMap(function ($product) {
            return collect($product['wholesale_prices'])->pluck('price');
        })->min();

        return [
            'min' => $minPrice,
            'max' => $maxPrice
        ];
    }

    private function retrieveCurrency(?WholesaleClient $client)
    {
        return optional($client)->preferred_currency_id ?? __hardcoded(2);
    }
}
