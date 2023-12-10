<?php

namespace App\Resolvers\Catalog;

use App\v2\Models\Product;

class CatalogProductQueryResolver
{
    public function resolve($filters, $store_id, $user_token = null)
    {
        $productQuery = Product::query()->whereIsSiteVisible(true);

        $isDubaiProductsNeed = count($filters[Product::FILTER_BRANDS]) > 0 && in_array(99999, $filters[Product::FILTER_BRANDS]);

        if (count ($filters[Product::FILTER_CATEGORIES]) > 0) {
            $productQuery->ofCategory($filters[Product::FILTER_CATEGORIES]);
        }

        if (count ($filters[Product::FILTER_SUBCATEGORIES]) > 0) {
            $productQuery->ofSubcategory($filters[Product::FILTER_SUBCATEGORIES]);
        }

        if (!$isDubaiProductsNeed && count ($filters[Product::FILTER_BRANDS]) > 0) {
            $productQuery->ofBrand($filters[Product::FILTER_BRANDS]);
        }

        if (count ($filters[Product::FILTER_PRICES]) > 0) {
            $productQuery->ofPrice($filters[Product::FILTER_PRICES]);
        }

        if ($filters[Product::FILTER_IS_HIT] === 'true') {
            $productQuery->isHit(Product::FILTER_IS_HIT);
        }

        if (count($filters[Product::FILTER_FILTERS]) > 0) {
            $productQuery->whereHasMorph('filters', function ($q) use ($filters) {
                return $q->whereIn('id', $filters[Product::FILTER_FILTERS]);
            });
        }

        if (strlen($filters[Product::FILTER_SEARCH]) > 0) {
            $productQuery->ofTag($filters[Product::FILTER_SEARCH]);
        }

        if ($isDubaiProductsNeed) {
            $productQuery->where('is_dubai', true);
        }

        $productQuery->when(\request()->has('iherb'), function ($query) {
            return $query->where('is_iherb', true);
        });

        $productQuery->when(\request()->has('is_iherb_hit'), function ($query) {
            return $query->where('is_iherb_hit', true);
        });

        $productQuery->whereHas('category', function ($q) {
            return $q->where('is_site_visible', true);
        });

        $productQuery->whereHas('subcategory', function ($q) {
            return $q->where('is_site_visible', true);
        });

        $productQuery->whereHas('batches', function ($q) use ($store_id) {
            if ($store_id === -1) {
                return $q->where('quantity', '>', 0)->whereIn('store_id', [1, 6]);
            } else {
                return $q->where('quantity', '>', 0)->where('store_id', $store_id);
            }
        });

        $productQuery->orderBy('product_name');

        return $productQuery;
    }
}
