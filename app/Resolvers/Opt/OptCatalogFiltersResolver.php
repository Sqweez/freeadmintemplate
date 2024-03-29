<?php

namespace App\Resolvers\Opt;

use App\v2\Models\Product;

class OptCatalogFiltersResolver
{

    const FILTER_ARRAY_KEYS = [
        Product::FILTER_CATEGORIES,
        Product::FILTER_SUBCATEGORIES,
        Product::FILTER_BRANDS,
        Product::FILTER_PRICES,
    ];

    public function resolve(array $params): array
    {
        $arrayFilters = [];
        foreach (self::FILTER_ARRAY_KEYS as $key) {
            if (isset($params[$key])) {
                $result = $this->prepareArrayFilters($params[$key]);
                if (!empty($result)) {
                    $arrayFilters[$key] = $result;
                }
            }
        }

        return $arrayFilters;
    }

    private function prepareArrayFilters($filters): array
    {
        return collect($filters)
            ->filter()
            ->map(function ($value) {
                if (preg_match('/^\d+$/', $value)) {
                    return $value;
                } else {
                    return \Arr::last(explode('-', $value));
                }
            })
            ->filter()
            ->values()
            ->toArray();
    }
}
