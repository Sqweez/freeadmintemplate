<?php

namespace App\Http\Controllers\api\v2;

use App\Category;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductCreateRequest;
use App\Http\Resources\RelatedProductsResource;
use App\Http\Resources\v2\Product\IHerbProductsResource;
use App\Http\Resources\v2\Product\ProductsResource;
use App\Http\Resources\v2\Product\ModeratorProducts;
use App\Http\Resources\v2\Product\ProductResource;
use App\MarginType;
use App\Store;
use App\v2\Models\Product;
use App\ProductBatch;
use App\v2\Models\ProductSaleEarning;
use App\v2\Models\ProductSku;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Validator;
use ProductService;

class ProductController extends Controller
{
    private function prepareSearchString($search) {
        return "%" . str_replace(' ', '%', $search) . "%";
    }

    /*
     * Получение всех товаров
     * */

    public function index(Request $request) {
        return ProductsResource::collection(ProductService::all());
    }

    /*
    * Получение одного товара
    */
    public function show($id) {
        return new ProductResource(ProductService::get($id));
    }

    public function store(ProductCreateRequest $request) {
        $product = ProductService::getProductFields($request);
        $product_attributes = ProductService::getRelationFields($request);
        $product_sku_attributes = ProductService::getSkuFields($request);
        $product = ProductService::create($product, $product_attributes);
        return ProductService::createSku($product, $product_sku_attributes);
    }

    public function createProductSku(Product $product, Request $request) {
        $product_sku_attributes = ProductService::getSkuFields($request);
        return ProductService::createSku($product, $product_sku_attributes);
    }

    public function updateProductSku(ProductSku $sku, Request $request) {
        $product_sku_attributes = ProductService::getSkuFields($request);
        return ProductService::updateSku($sku, $product_sku_attributes);
    }

    public function getProductsQuantity($store) {
        if (intval($store) > 0) {
            return ProductBatch::query()
                ->quantitiesOfStore($store)
                ->get();
        }
        return ProductBatch::query()
            ->quantities()
            ->get()
            ->groupBy('product_id')
            ->map(function ($item) {
                return collect($item)->groupBy('store_id');
            })
            ->map(function($product, $productId) {
                $storesQuantity = collect($product)->map(function ($store, $storeId) {
                    return [
                        'store_id' => $storeId,
                        'quantity' => collect($store)->reduce(function ($a, $c) {
                            return $a + $c['quantity'];
                        }, 0),
                        'name' => collect($store)->first()['store']['name']
                    ];
                })->values()->all();
                $totalQuantity = collect($storesQuantity)->reduce(function ($a, $c) {
                    return $a + $c['quantity'];
                }, 0);
                $totalQuantity = [
                    'store_id' => - 1,
                    'quantity' => $totalQuantity,
                    'name' => 'Всего'
                ];
                return array_merge([$totalQuantity], $storesQuantity);
            });
    }

    public function update(Product $product, Request $request) {
        $product_attributes = ProductService::getProductFields($request);
        $product_fields = ProductService::getRelationFields($request);
        ProductService::updateProduct($product, $product_attributes, $product_fields);
        $product->fresh();
        if ($request->get('grouping_attribute_id') === 0 || $request->get('grouping_attribute_id') === null) {
            $productSku = ProductSku::whereProductId($product->id)->first();
            $product_sku_attributes = ProductService::getSkuFields($request);
            ProductService::updateSku($productSku, $product_sku_attributes);
        }
        return ProductsResource::collection(ProductSku::whereProductId($product->id)->with(ProductSku::PRODUCT_SKU_WITH_ADMIN_LIST)->get());
    }

    public function delete($id) {
        ProductService::delete($id);
    }


    public function addProductQuantity($id, Request $request) {
        $validator = Validator::make($request->all(), [
            'store_id' => 'required|numeric',
            'quantity' => 'required|numeric',
            'purchase_price' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Переданы некорректные данные'
            ], 422);
        }

        $store_id = $request->get('store_id');
        $quantity = $request->get('quantity');
        $purchase_price = $request->get('purchase_price');

        ProductService::addQuantity($id, $store_id, $quantity, $purchase_price);
        return ProductService::getQuantityByProduct($id, $store_id);
    }

    public function changeCount($id, Request $request) {
        $batchQuery = ProductBatch::query()
            ->where('store_id', $request->get('store_id'))
            ->where('product_id', $id);

        if (intval($request->get('increment')) === -1) {
            $batchQuery->where('quantity', '>', 0);
        }

        $batch = $batchQuery->orderBy('created_at', 'desc')->first();

        if (!$batch) {
            return response()->json(['message' => 'По данному товару не было поставок!'], 500);
        }

        $batch->quantity += $request->get('increment');

        $batch->save();

        return ProductBatch::where('quantity', '>', 0)
            ->whereStoreId($request->get('store_id'))
            ->whereProductId($id)
            ->groupBy('product_id')
            ->select('product_id')
            ->selectRaw('sum(quantity) as quantity')
            ->first();
    }

    public function related(Request $request): AnonymousResourceCollection {
        return RelatedProductsResource::collection(Category::with(
            [
                'relatedProducts', 'relatedProducts.product', 'relatedProducts.product.manufacturer', 'relatedProducts.product.category'
            ]
        )->get());
    }

    public function relatedCreate(Request $request) {
        $products = $request->get('products');
        $category = $request->get('category_id');
        $category = Category::find($category);
        $products = array_map(function ($item) {
            return [
                'product_id' => $item
            ];
        }, $products);
        $category->relatedProducts()->delete();
        $category->relatedProducts()->createMany($products);
        return new RelatedProductsResource(Category::with([
            'relatedProducts', 'relatedProducts.product', 'relatedProducts.product.manufacturer', 'relatedProducts.product.category'
        ])->whereKey($category)->first());
    }

    public function getProductBalance() {

        $userRole = auth()->user()->role ?? null;
        $isFranchise = $userRole && $userRole->role_name === 'Франшиза';
        $batches = ProductBatch::query()
            ->when($isFranchise, function ($builder) {
                return $builder->where('store_id', auth()->user()->store_id);
            })
            ->where('quantity', '>', 0)
            ->with('product', 'product.product:id,product_price,product_name')
            ->get();

        $batches = $batches->map(function ($item) {
            return [
                'id' => $item['id'],
                'quantity' => $item['quantity'],
                'store_id' => $item['store_id'],
                'purchase_price' => $item['purchase_price'],
                'product_price' => $item['product']['product']['product_price'] ?? 0,
                //'product' => $item['product']['product']['product_name']
            ];
        })
            ->filter(function($item) { return $item['product_price'] > 0;})
            ->values()
            ->groupBy('store_id');


        $purchasePrices = $batches->map(function ($items, $key) {
            return collect($items)->reduce(function ($a, $c) {
                return $a + $c['purchase_price'] * $c['quantity'];
            }, 0);
        })->toArray();
        $productPrices = $batches->map(function ($items, $key) {
            return collect($items)->reduce(function ($a, $c) {
                return $a + $c['product_price'] * $c['quantity'];
            }, 0);
        })->toArray();

        return [
            'purchase_prices' => $purchasePrices,
            'product_prices' => $productPrices
        ];
    }

    public function moderatorProducts() {
        return ModeratorProducts::collection(
            ProductSku::query()
                ->with(ProductSku::PRODUCT_SKU_MODERATOR_LIST)
                ->orderBy('product_id')
                ->orderBy('id')
                ->get()
                ->sortBy('product_name')
        );
    }

    public function outOfStockProducts(Request $request) {
        $store_id = $request->get('store_id');
        $date = now()->subDays(60);
        return ProductBatch::whereStoreId($store_id)
            ->get()
            ->groupBy('product_id')
            ->map(function ($batch, $key) use ($date) {
                /*$_batch = $batch;
                $batch['has_batches'] = collect($_batch)->filter(function ($item) use ($date) {
                    return Carbon::parse($item['created_at'])->gte($date);
                })->count();
                $batch['quantity'] = collect($_batch)->values();*/
                return [
                    'has_batches' => collect($batch)->filter(function ($item) use ($date) {
                            return Carbon::parse($item['created_at'])->gte($date);
                        })->count() > 0,
                    'product_id' => $key,
                    'quantity' => collect($batch)->values()->reduce(function ($a, $c) {
                        return $a + $c['quantity'];
                    }, 0)
                ];
            })->values()->filter(function ($item) {
                return $item['has_batches'] && $item['quantity'] <= 3;
            })->values();
    }

    public function getProductSellerEarning() {
        return ProductSaleEarning::all();
    }

    public function setProductSellerEarning(Request $request) {
        $products = $request->get('products');
        $earnings = $request->get('earnings');

        ProductSaleEarning::query()
            ->whereIn('product_id', $products)
            ->delete();

        collect($products)->each(function ($product) use ($earnings) {
            collect($earnings)->each(function ($store) use ($product) {
                ProductSaleEarning::create([
                    'product_id' => $product,
                    'percent' => $store['percent'],
                    'store_id' => $store['id']
                ]);
            });
        });
    }

    public function getMarginTypes() {
        return MarginType::all();
    }

    public function setMarginTypes(Request $request) {
        $products = $request->get('products');
        $type = $request->get('margin_type');
        ProductSku::whereIn('id', $products)->update(['margin_type_id' => $type]);
        $marginType = MarginType::find($type)->only(['id', 'title']);
        return collect($products)->map(function ($product) use ($marginType) {
            return [
                'id' => $product,
                'margin_type' => $marginType
            ];
        });
    }

    public function editMarginTypes(Request $request) {
        $types = $request->get('types');
        collect($types)->each(function ($type) {
            MarginType::whereKey($type['id'])->update($type);
        });
    }

    public function setProductTags(Request $request) {
        $tags = $request->get('tags');
        $products = Product::find($request->get('products'));
        ProductService::attachTags($products, $tags);
        return response([], 200);
    }

    public function deleteProductTag(Request $request) {
        $product = Product::find($request->get('product_id'));
        $product->tags()->detach($request->get('tag_id'));
        return response([]);
    }

    public function getIherbProducts(): AnonymousResourceCollection {
        $sku = ProductSku::query()
            ->whereHas('product', function ($q) {
                return $q->where('is_iherb', true);
            })
            ->with(ProductSku::PRODUCT_SKU_WITH_ADMIN_LIST)
            ->with('batches')
            ->get();

        return IHerbProductsResource::collection($sku);
    }
}
