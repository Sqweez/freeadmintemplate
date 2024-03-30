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
        \Log::info('params', $params);
        $arrayFilters = [];
        foreach (self::FILTER_ARRAY_KEYS as $key) {
            if (isset($params[$key])) {
                $result = $this->prepareArrayFilters(json_decode($params[$key]));
                $arrayFilters[$key] = $result;
            }
        }

        if (isset($params[Product::FILTER_SEARCH])) {
            $arrayFilters[Product::FILTER_SEARCH] = str_replace(' ', '%', $params[Product::FILTER_SEARCH]) . "%";
        }

        return array_filter($arrayFilters);
    }

    private function prepareArrayFilters($filters): ?array
    {
        return collect($filters)
            ->map(function ($value) {
                if (preg_match('/^\d+$/', $value)) {
                    return $value;
                } else {
                    return \Arr::last(explode('-', $value));
                }
            })
            ->values()
            ->toArray();
    }
}
