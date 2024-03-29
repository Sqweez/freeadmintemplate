<?php

namespace App\Resolvers\Opt;

use App\v2\Models\Product;
use App\v2\Models\WholesaleClient;

class OptCatalogProductResolver
{
    public function resolver(array $filters, ?WholesaleClient $client)
    {
        $currencyId = $this->retrieveCurrency($client);
        $query = Product::query()
            ->OptProducts()
            ->when(!empty($filters[Product::FILTER_CATEGORIES]), function ($query) use ($filters) {
                return $query->ofCategory($filters[Product::FILTER_CATEGORIES]);
            })
            ->when(!empty($filters[Product::FILTER_SUBCATEGORIES]), function ($query) use ($filters) {
                return $query->ofSubcategory($filters[Product::FILTER_SUBCATEGORIES]);
            })
            ->when(!empty($filters[Product::FILTER_BRANDS]), function ($query) use ($filters) {
                return $query->ofBrand($filters[Product::FILTER_BRANDS]);
            })
            ->when(!empty($filters[Product::FILTER_PRICES]), function ($query) use ($filters) {
                return $query->ofPrice($filters[Product::FILTER_PRICES]);
            })
            ->when(!empty($filters[Product::FILTER_SEARCH]), function ($query) use ($filters) {
                return $query->ofTag($filters[Product::FILTER_SEARCH]);
            })
            ->when(!empty($filters[Product::FILTER_FILTERS]), function ($query) use ($filters) {
                return $query->whereHas('filters', function ($q) use ($filters) {
                    return $q->whereIn('id', $filters[Product::FILTER_FILTERS]);
                });
            })
            ->with('subcategory')
            ->with('product_thumbs')
            ->whereHas('wholesale_prices', function ($query) use ($currencyId) {
                return $query->where('currency_id', $currencyId);
            })
            ->with(['wholesale_prices' => function ($query) use ($currencyId) {
                return $query->where('currency_id', $currencyId);
            }]);

        return $query;
    }

    private function retrieveCurrency(?WholesaleClient $client)
    {
        return optional($client)->preferred_currency_id ?? __hardcoded(2);
    }
}
