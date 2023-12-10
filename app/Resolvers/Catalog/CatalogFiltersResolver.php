<?php

namespace App\Resolvers\Catalog;

use App\v2\Models\Product;

class CatalogFiltersResolver
{
    public function resolve(array $params): array
    {
        return [
            Product::FILTER_CATEGORIES => array_map('intval', array_filter(explode(',', ($params[Product::FILTER_CATEGORIES] ?? '')), 'strlen')),
            Product::FILTER_SUBCATEGORIES => array_map('intval', array_filter(explode(',', ($params[Product::FILTER_SUBCATEGORIES] ?? '')), 'strlen')),
            Product::FILTER_BRANDS => array_map('intval', array_filter(explode(',', ($params[Product::FILTER_BRANDS] ?? '')), 'strlen')),
            Product::FILTER_PRICES => array_map('intval', array_filter(explode(',', ($params[Product::FILTER_PRICES] ?? '')), 'strlen')),
            Product::FILTER_IS_HIT => isset($params[Product::FILTER_IS_HIT]) ? ($params[Product::FILTER_IS_HIT] === 'true' ? 'true' : 'false') : 'false',
            Product::FILTER_IS_IHERB_HIT => isset($params[Product::FILTER_IS_IHERB_HIT]) ? ($params[Product::FILTER_IS_IHERB_HIT] === 'true' ? 'true' : 'false') : 'false',
            Product::FILTER_SEARCH => isset($params[Product::FILTER_SEARCH]) ? str_replace(' ', '%', $params[Product::FILTER_SEARCH]) . "%" : '',
            PRODUCT::FILTER_STORE => $params['store_id'] ?? __hardcoded(1),
            Product::FILTER_FILTERS => isset($params['filters']) ? json_decode($params['filters']) : [],
        ];
    }
}
