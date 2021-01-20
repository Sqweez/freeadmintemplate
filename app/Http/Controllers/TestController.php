<?php

namespace App\Http\Controllers;

use App\Arrival;
use App\Attribute;
use App\AttributeProduct;
use App\Category;
use App\CategoryProduct;
use App\Client;
use App\Http\Resources\shop\ProductResource;
use App\Http\Resources\ReportResource;
use App\Http\Resources\v2\Product\ProductsResource;
use App\Http\Resources\TransferResource;
use App\Http\Resources\v2\ProductListResource;
use App\Http\Resources\v2\Report\ReportsResource;
use App\Http\Resources\v2\Sku\SkuResource;
use App\Manufacturer;
use App\ManufacturerProducts;
use App\SaleProduct;
use App\v2\Models\BaseProduct;
use App\v2\Models\Product;
use App\ProductBatch;
use App\ProductImage;
use App\ProductQuantity;
use App\ProductTag;
use App\Sale;
use App\Subcategory;
use App\Tag;
use App\Transfer;
use App\v2\Models\AttributeValue;
use App\v2\Models\ProductAttribute;
use App\v2\Models\ProductSku;
use App\v2\Models\Sku;
use Carbon\Carbon;
use Barryvdh\Debugbar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use ProductService;

class TestController extends Controller
{

    public function ungroupped(Request $request) {
        Client::where('client_city', '=', 1)->update([
           'client_city' => 9
       ]);
        Client::where('client_city', '=', 2)->update([
            'client_city' => 13
        ]);
        Client::where('client_city', '=', 3)->update([
            'client_city' => 12
        ]);
        Client::where('client_city', '=', 4)->update([
            'client_city' => 10
        ]);
        Client::where('client_city', '=', 5)->update([
            'client_city' => 5
        ]);
    }

    public function ungroupProduct($id) {
        $product = Product::whereKey($id)->first();
        $product->grouping_attribute_id = null;
        $product->save();
        $productSku = ProductSku::whereProductId($id)->first();
        DB::table('attributable')
            ->where('attributable_id', $productSku->id)
            ->where('attributable_type', 'App\v2\Models\ProductSku')
            ->update([
                'attributable_id' => $id,
                'attributable_type' => 'App\v2\Models\Product'
            ]);

        DB::table('attributable')
            ->where('attributable_id', $id)
            ->where('attributable_type', 'App\v2\Models\Product')
            ->whereIn('attribute_value_id', [36, 7])
            ->delete();

        return back();
    }

    public function index(Request $request) {
        $products = ProductSku::whereIn('product_id', [3, 2, 1271, 1320, 1205, 825, 600, 601, 602, 624, 621, 10, 29, 73, 98, 99, 198, 718, 1059, 171, 1002, 449, 724, 1315, 192, 612, 109, 385, 1304, 751])->get()->pluck('id');
        $sales = Sale::whereDate('created_at', '>=', '2020-12-10')
            ->whereDate('created_at', '<=', '2021-01-10')
            ->whereHas('products', function ($q) use ($products) {
                return $q->whereIn('product_id', $products);
            })
            /*->with(['products' => function ($q) use ($products) {
                return $q->whereIn('product_id', $products);
            }])*/
            ->with(['products.product.product:id,product_name', 'user:id,name'])
            ->get()->filter(function ($sale) {
                return count($sale['products']) > 0;
            })->values()->map(function ($sales)  use ($products) {
                $products = collect($sales['products'])->filter(function ($product) use ($products) {
                    return $products->contains($product['product_id']);
                })->values()->map(function ($product) use ($sales){
                    return [
                        'product_id' => $product['product']['product_id'],
                        'product_name' => $product['product']['product']['product_name'],
                    ];
                });
                return [
                    'products' => $products,
                    'user_id' => $sales['user_id'],
                    'user' => $sales['user']['name'],
                    'id' => $sales['id']
                ];
            })
            ->groupBy('user_id')->map(function ($sale, $key) {
                $products = collect($sale)->pluck('products')
                    ->flatten(1)
                    ->groupBy('product_id')->map(function ($product) {
                        return [
                            'count' => count($product),
                          /*  'product_name' => $product[0]['product_name'],*/
                            'product_id' => $product[0]['product_id'],
                        ];
                    })->values();
                return [
                    'products' => $products,
                    'user' => $sale[0]['user']
                ];
            })->values();
        return view('test', [
            'products' => Product::find([3, 2, 1271, 1320, 1205, 825, 600, 601, 602, 624, 621, 10, 29, 73, 98, 99, 198, 718, 1059, 171, 1002, 449, 724, 1315, 192, 612, 109, 385, 1304, 751]),
            'sales' => $sales,
        ]);
    }

    public function getBrands($filters, $store_id) {
        $_filters = $filters;
        $_filters['brands'] = [];
        $ids = $this->getProductWithFilter($_filters, $store_id)->without(['subcategory', 'attributes', 'product_images'])->select(['manufacturer_id'])->groupBy(['manufacturer_id'])->get()->pluck('manufacturer_id');
        return Manufacturer::whereIn('id', $ids)->get();
    }

    private function getProductWithFilter($filters, $store_id) {
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

        $productQuery->inStock($store_id);

        $productQuery->with(['subcategory', 'attributes', 'product_images']);

        return $productQuery;
    }
}
