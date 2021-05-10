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
        $products = json_decode($request->get('products'));
        $products_id = ProductSku::whereIn('product_id', $products)
            ->select(['id'])
            ->get()
            ->pluck('id');

        $date_start = $request->get('date_start');
        $date_finish = $request->get('date_finish');
        $user_id = $request->has('user_id') ? $request->get('user_id') : null;
        $store_id = $request->has('store_id') ? $request->get('store_id') : null;

        $salesQuery = Sale::query()->whereDate('created_at', '>=', $date_start)
            ->whereDate('created_at', '<=', $date_finish)
            ->whereHas('products', function ($q) use ($products_id) {
                return $q->whereIn('product_id', $products_id);
            })
            ->with(['products', 'products.product', 'products.product.product:id,product_name,manufacturer_id,product_price'])
            ->with(['products.product.product:id,product_name,manufacturer_id'])
            ->with(['products.product.product.manufacturer', 'products.product.product.attributes', 'products.product.attributes']);

        if ($user_id) {
            $salesQuery = $salesQuery->whereUserId($user_id);
        }

        if ($store_id) {
            $salesQuery = $salesQuery->whereStoreId($store_id);
        }


        return view('test', [
            'sales' => $salesQuery->get()
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
        $store_id = $request->get('store_id');
        $user_token = $request->get('user_token');
        $categories = Category::all();

        $productQuery = Product::query()->whereIsSiteVisible(true);

        $productQuery->with(['subcategory', 'attributes', 'product_thumbs', 'product_images']);
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

        $products = $productQuery->limit(10)->get();

        $categories = $categories->map(function ($category) use ($products) {
            $_products = $products->filter(function ($p) use ($category) {
                return $category['id'] === $p['category_id'];
            });
            $category['products'] = \App\Http\Resources\shop\ProductsResource::collection($_products)->toArray(\request());
            return $category;
        })->filter(function ($category) {
            return count($category['products']) > 0;
        })->values();
        return view('test', [
            'test' => $categories->toArray()
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
