<?php

namespace App\Http\Controllers\api\shop;

use App\Category;
use App\Http\Controllers\Controller;
use App\Http\Resources\shop\ProductResource;
use App\Http\Resources\shop\ProductsResource;
use App\Manufacturer;
use App\v2\Models\ProductSku;
use App\v2\Models\Product;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class ProductController extends Controller {

    public function getProducts(Request $request) {
        $query = $request->except('store_id');
        $store_id = intval($request->get('store_id', 1));
        $user_token = $request->get('user_token');
        $all_products = $request->has('all_products');
        if ($all_products) {
            return $this->getAllProducts($request->get('category'));
        } else {
            return $this->getFilteredProducts($query, $store_id, $user_token);
        }
    }

    public function filters(Request $request) {
        $query = $request->all();
        $store_id = $request->cookie('store_id') ?? 1;
        return $this->getFilters($query, $store_id);
    }

    private function prepareSearchString($search) {
        return "%" . str_replace(' ', '%', $search) . "%";
    }

    private function getFilters($query, $store_id) {
        $filters = $this->getFilterParametrs($query, $store_id);
        return [
            'brands' => $this->getBrands($filters, $store_id),
            'prices' => $this->getPrices($filters, $store_id),
        ];

    }
    private function getBrands($filters, $store_id) {
        $_filters = $filters;
        $_filters['brands'] = [];
        $ids = $this->getProductWithFilter($_filters, $store_id)->without(['subcategory', 'attributes', 'product_images'])->select(['manufacturer_id'])->groupBy(['manufacturer_id'])->get()->pluck('manufacturer_id');
        return Manufacturer::whereIn('id', $ids)->get();
    }

    private function getPrices($filters, $store_id) {
        $_filters = $filters;
        $_filters['prices'] = [];
        $productsPrices = $this->getProductWithFilter($_filters, $store_id)->without(['subcategory', 'attributes', 'product_images'])->groupBy('product_price')->select(['product_price'])->get()->pluck('product_price');
        return [
            $productsPrices->min(),
            $productsPrices->max()
        ];
    }

    private function convertToArray($filters) {
        $array = [];

        foreach ($filters as $key => $filter) {
            $array[] = $filter;
        }

        return $array;
    }


    private function getFilterParametrs($query, $store_id) {
        return [
            Product::FILTER_CATEGORIES => array_map('intval', array_filter(explode(',', ($query[Product::FILTER_CATEGORIES] ?? '')), 'strlen')),
            Product::FILTER_SUBCATEGORIES => array_map('intval', array_filter(explode(',', ($query[Product::FILTER_SUBCATEGORIES] ?? '')), 'strlen')),
            Product::FILTER_BRANDS => array_map('intval', array_filter(explode(',', ($query[Product::FILTER_BRANDS] ?? '')), 'strlen')),
            Product::FILTER_PRICES => array_map('intval', array_filter(explode(',', ($query[Product::FILTER_PRICES] ?? '')), 'strlen')),
            Product::FILTER_IS_HIT => isset($query[Product::FILTER_IS_HIT]) ? ($query[Product::FILTER_IS_HIT] === 'true' ? 'true' : 'false') : 'false',
            // Product::FILTER_SEARCH => isset($query[Product::FILTER_SEARCH]) ? $this->prepareSearchString($query[Product::FILTER_SEARCH]) : ''
            // @TODO 2022-04-17T22:12:44 maybe rework it
            Product::FILTER_SEARCH => isset($query[Product::FILTER_SEARCH]) ? str_replace(' ', '%', $query[Product::FILTER_SEARCH]) . "%" : ''
        ];
    }

    private function getProductWithFilter($filters, $store_id, $user_token = null) {
        $productQuery = Product::query()->whereIsSiteVisible(true);

        if (count ($filters[Product::FILTER_CATEGORIES]) > 0) {
            $productQuery->ofCategory($filters[Product::FILTER_CATEGORIES]);
        }

        if (count ($filters[Product::FILTER_SUBCATEGORIES]) > 0) {
            $productQuery->ofSubcategory($filters[Product::FILTER_SUBCATEGORIES]);
        }

        if (count ($filters[Product::FILTER_BRANDS]) > 0) {
            $productQuery->ofBrand($filters[Product::FILTER_BRANDS]);
        }

        if (count ($filters[Product::FILTER_PRICES]) > 0) {
            $productQuery->ofPrice($filters[Product::FILTER_PRICES]);
        }

        if ($filters[Product::FILTER_IS_HIT] === 'true') {
            $productQuery->isHit(Product::FILTER_IS_HIT);
        }

        if (strlen($filters[Product::FILTER_SEARCH]) > 0) {
            $productQuery->ofTag($filters[Product::FILTER_SEARCH]);
        }

        $productQuery->whereHas('category', function ($q) {
            return $q->where('is_site_visible', true);
        });

        $productQuery->whereHas('subcategory', function ($q) {
            return $q->where('is_site_visible', true);
        });

        $productQuery->with(['subcategory', 'attributes', 'product_thumbs', 'product_images', 'stocks']);
        $productQuery->with(['favorite' => function ($query) use ($user_token) {
            return $query->where('user_token', $user_token);
        }]);

        $productQuery->whereHas('batches', function ($q) use ($store_id) {
            if ($store_id === -1) {
                return $q->where('quantity', '>', 0)->whereIn('store_id', [1, 6]);
            } else {
                return $q->where('quantity', '>', 0)->where('store_id', $store_id);
            }
        })->with(['batches' => function ($q) use ($store_id) {
            if ($store_id === -1) {
                return $q->where('quantity', '>', 0)->whereIn('store_id', [1, 6]);
            } else {
                return $q->where('quantity', '>', 0)->where('store_id', $store_id);
            }
        }]);

        $productQuery->orderBy('product_name');

        return $productQuery;
    }

    private function getAllProducts($category_id) {
        $productQuery = Product::query()->whereIsSiteVisible(true);
        $productQuery->whereCategoryId($category_id);
        $productQuery->whereHas('category', function ($q) {
            return $q->where('is_site_visible', true);
        });
        $productQuery->whereHas('subcategory', function ($q) {
            return $q->where('is_site_visible', true);
        });
        $productQuery->orderBy('product_name');
        return $productQuery
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product['id'],
                    'slug' => \Str::slug($product['product_name'])
                ];
            });
    }

    private function getFilteredProducts($query, $store_id, $user_token): AnonymousResourceCollection {
        $filters = $this->getFilterParametrs($query, $store_id);
        $products = $this->getProductWithFilter($filters, $store_id, $user_token)
            ->paginate(36);
        return ProductsResource::collection(
            $products
        );
    }


    public function getProduct(Product $product) {
        return new ProductResource(
            Product::with([
                'sku', 'sku.attributes', 'sku.batches', 'product_images', 'attributes', 'stocks', 'comments', 'comments.user', 'comments.client', 'manufacturer'])
                ->whereKey($product->id)
                ->first()
        );
    }

    public function getStockProducts(Request $request) {
        $store_id = intval($request->get('store_id')) || -1;
        $user_token = $request->get('user_token');
        $stock_id = $request->get('stock_id');
        $productQuery = Product::query()->whereIsSiteVisible(true);
        $productQuery->with(['subcategory', 'attributes', 'product_thumbs', 'product_images', 'stocks']);
        $productQuery->with(['favorite' => function ($query) use ($user_token) {
            return $query->where('user_token', $user_token);
        }]);

        $productQuery->whereHas('batches', function ($q) use ($store_id) {
            if ($store_id === -1) {
                return $q->where('quantity', '>', 0)->whereIn('store_id', [1, 6]);
            } else {
                return $q->where('quantity', '>', 0)->where('store_id', $store_id);
            }
        })->with(['batches' => function ($q) use ($store_id) {
            if ($store_id === -1) {
                return $q->where('quantity', '>', 0)->whereIn('store_id', [1, 6]);
            } else {
                return $q->where('quantity', '>', 0)->where('store_id', $store_id);
            }
        }]);

        $productQuery->whereHas('stocks', function ($q) use ($stock_id) {
            return $q->where('stock_id', $stock_id);
        });

        $products = $productQuery->get();

        return ProductsResource::collection(
            $products
        );
    }

    public function getHitProducts(Request $request) {
        $store_id = intval($request->get('store_id')) || -1;
        $user_token = $request->get('user_token');
        $categories = Category::select(['id', 'category_name'])->get();

        $productQuery = Product::query()->whereIsSiteVisible(true)->whereIsHit(true);

        $productQuery->with(['subcategory', 'attributes', 'product_thumbs', 'product_images', 'stocks']);
        $productQuery->with(['favorite' => function ($query) use ($user_token) {
            return $query->where('user_token', $user_token);
        }]);

        $productQuery->with(['sku.batches' => function ($q) {
            return $q->where('quantity', '>', 0);
        }]);

        $products = $productQuery->get();

        return $categories->map(function ($category) use ($products) {
            $_products = $products->filter(function ($p) use ($category) {
                return $category['id'] === $p['category_id'];
            });
            $category['products'] = collect(ProductsResource::collection($_products))
                ->map(function ($i) use ($_products) {
                    $needle = $_products->where('id', $i['product_id'])->first();
                    // @TODO 2022-05-24T22:05:44 ugly rework
                    $batches = [];
                    foreach ($needle['sku'] as $item) {
                        foreach ($item['batches'] as $batch) {
                            $batches[] = $batch;
                        }
                    }
                    $batches = collect($batches)
                        ->groupBy('store_id')
                        ->map(function ($v, $k) {
                            return [
                                'store_id' => $k,
                                'quantity' => collect($v)->reduce(function ($a, $c) {
                                    return $a + $c['quantity'];
                                }, 0)
                            ];
                        })
                        ->values();
                    $i['batches'] = $batches;
                    return $i;
                });
            return $category;
        })->filter(function ($category) {
            return count($category['products']) > 0;
        })->values()->sortBy('id')->values();
    }

}
