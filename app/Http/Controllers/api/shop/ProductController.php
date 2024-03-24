<?php

namespace App\Http\Controllers\api\shop;

use App\Arrival;
use App\Category;
use App\Http\Controllers\Controller;
use App\Http\Resources\shop\ProductResource;
use App\Http\Resources\shop\ProductsResource;
use App\Manufacturer;
use App\Resolvers\Catalog\CatalogFiltersResolver;
use App\Resolvers\Catalog\CatalogProductQueryResolver;
use App\v2\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class ProductController extends Controller {


    private CatalogFiltersResolver $filtersResolver;
    private CatalogProductQueryResolver $catalogProductQueryResolver;
    public function __construct()
    {
        $this->filtersResolver = app(CatalogFiltersResolver::class);
        $this->catalogProductQueryResolver = app(CatalogProductQueryResolver::class);
    }

    public function getProducts(Request $request) {
        $query = $request->except('store_id');
        $store_id = intval($request->get('store_id', 16));
        if ($store_id === 0) {
            $store_id = 16;
        }
        /*if ($store_id === 16) {
            $request->merge(['store_id' => 31]);
            $store_id = 31;
        }*/
        $user_token = $request->get('user_token');
        $all_products = $request->has('all_products');
        if ($all_products) {
            return $this->getAllProducts($request->get('category'));
        } else {
            return $this->getFilteredProducts($query, $store_id, $user_token);
        }
    }

    public function filters(Request $request): array
    {
        $query = $request->all();
        $store_id = $request->cookie('store_id') ?? 1;
        return $this->getFilters($query, $store_id);
    }

    private function prepareSearchString($search): string
    {
        return "%" . str_replace(' ', '%', $search) . "%";
    }

    private function getFilters($query, $store_id): array
    {
        $filters = $this->getFilterParametrs($query, $store_id);
        return [
            'brands' => $this->getBrands($filters, $store_id),
            'prices' => $this->getPrices($filters, $store_id),
        ];

    }
    private function getBrands($filters, $store_id): Collection
    {
        $_filters = $filters;
        $_filters['brands'] = [];
        $ids = $this->getProductWithFilter($_filters, $store_id)->without(['subcategory', 'attributes', 'product_images'])->select(['manufacturer_id'])->groupBy(['manufacturer_id'])->get()->pluck('manufacturer_id');
        return Manufacturer::whereIn('id', $ids)->get();
    }

    private function getPrices($filters, $store_id): array
    {
        $_filters = $filters;
        $_filters['prices'] = [];
        $productsPrices = $this->getProductWithFilter($_filters, $store_id)->without(['subcategory', 'attributes', 'product_images'])->groupBy('product_price')->select(['product_price'])->get()->pluck('product_price');
        return [
            $productsPrices->min(),
            $productsPrices->max()
        ];
    }

    private function convertToArray($filters): array
    {
        $array = [];

        foreach ($filters as $key => $filter) {
            $array[] = $filter;
        }

        return $array;
    }


    private function getFilterParametrs($query, $store_id): array
    {
        return $this->filtersResolver->resolve($query);
    }

    private function getProductWithFilter($filters, $store_id, $user_token = null) {

        $productQuery = $this->catalogProductQueryResolver->resolve($filters, $store_id, $user_token);

        $productQuery->with(['subcategory', 'attributes', 'product_thumbs', 'product_images', 'stocks']);
        $productQuery->with(['favorite' => function ($query) use ($user_token) {
            return $query->where('user_token', $user_token);
        }]);

        $productQuery->with(['batches' => function ($q) use ($store_id) {
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
        $productQuery->when(\request()->has('iherb'), function ($query) {
            return $query->where('is_iherb', true);
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
            ->paginate(24);
        return ProductsResource::collection(
            $products
        );
    }


    public function getProduct(Product $product): ProductResource
    {
        return new ProductResource(
            Product::with([
                'sku',
                'sku.attributes',
                'sku.batches',
                'product_images',
                'attributes',
                'stocks',
                'comments',
                'comments.user',
                'comments.client',
                'manufacturer',
                'filters'
            ])
                ->whereKey($product->id)
                ->first()
        );
    }

    public function getStockProducts(Request $request): AnonymousResourceCollection
    {
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

    public function getIherbHitProducts(Request $request): Collection {
        $store_id = intval($request->get('store_id')) || -1;
        $user_token = $request->get('user_token');
        $productQuery = Product::query()
            ->whereIsSiteVisible(true)
            ->whereIsIherbHit(true)
            ->where('is_iherb', true);

        $productQuery->with(['subcategory', 'attributes', 'product_thumbs', 'product_images', 'stocks', 'category', 'batches']);
        $productQuery->with(['favorite' => function ($query) use ($user_token) {
            return $query->where('user_token', $user_token);
        }]);

        $productQuery->with(['sku.batches' => function ($q) {
            return $q->where('quantity', '>', 0);
        }]);

        $products = $productQuery->get();

        return collect(ProductsResource::collection($products))
            ->map(function ($i) use ($products) {
                $needle = $products->where('id', $i['product_id'])->first();
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
    }

    public function getHitProducts(Request $request) {
        $store_id = intval($request->get('store_id')) || -1;
        $user_token = $request->get('user_token');
        $categories = Category::select(['id', 'category_name'])->get();

        $productQuery = Product::query()->whereIsSiteVisible(true)->whereIsHit(true);

        $productQuery->with(['subcategory', 'attributes', 'product_thumbs', 'product_images', 'stocks', 'category', 'batches']);
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
            $_products = $_products->sort(function ($a, $b) {
                if ($a->margin_type_id == 4 && $b->margin_type_id != 4) {
                    return -1;
                } elseif ($a->margin_type_id != 4 && $b->margin_type_id == 4) {
                    return 1;
                } else {
                    return 0;
                }
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

    public function getLastArrivalProducts(Request $request) {
        $store_id = intval($request->get('store_id', 16));
        $arrival = Arrival::query()
            ->where('is_completed', 1)
            ->latest()
            ->first();

        $productsIds = $arrival->products->pluck('product_id');

        $products = Product::query()
            ->whereHas('sku', function ($query) use ($productsIds) {
                return $query->whereIn('id', $productsIds);
            })
            ->whereIsSiteVisible(true)
            ->with(['subcategory', 'attributes', 'product_thumbs', 'product_images', 'stocks'])
            ->whereHas('batches', function ($q) use ($store_id) {
                if ($store_id === -1) {
                    return $q->where('quantity', '>', 0)->whereIn('store_id', [1, 6]);
                } else {
                    return $q->where('quantity', '>', 0)->where('store_id', $store_id);
                }
            })
            ->get();

        return ProductsResource::collection(
            $products
        );
    }
}
