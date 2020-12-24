<?php

namespace App\Http\Controllers;

use App\Arrival;
use App\AttributeProduct;
use App\Category;
use App\CategoryProduct;
use App\Http\Resources\v2\Product\ProductResource;
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
    public function index(Request $request) {

        $start = $request->get('start');
        $finish = $request->get('finish');
        $reports =  ReportsResource::collection(
            Sale::with(['client', 'user', 'store','products.product', 'products.product.product:id,product_name,manufacturer_id'])
                ->with(['products' => function ($query) {
                    return $query->groupBy('product_id')->addSelect(\DB::raw('*, count(*) as product_count'));
                }])
                ->take(50)
                ->report([$start, $finish])
                ->get()
        );

        return view('test', [
            'product' => $reports->toArray($request)
        ]);
    }

    public function index2() {

    }

    public function getProducts(Request $request) {
        $query = $request->except('store_id');
        $store_id = $request->get('store_id') ?? 1;
        return $this->getFilteredProducts($query, $store_id);
    }

    public function filters(Request $request) {
        $query = $request->all();
        $store_id = $request->cookie('store_id') ?? 1;
        return $this->getFilters($query, $store_id);
    }

    public function getBySearch(Request $request) {
        $search = $this->prepareSearchString($request->get('search') ?? "");
        return Product::ofSearch($search)->paginate(15);
    }

    private function prepareSearchString($search) {
        return "%" . str_replace(' ', '%', $search) . "%";
    }

    private function getFilters($query, $store_id) {
        $filters = $this->getFilterParametrs($query, $store_id);
        return [
            'brands' => array_filter($this->convertToArray($this->getBrands($filters, $store_id)), function ($i) { return $i; }),
            'prices' => $this->getPrices($filters, $store_id),
        ];

    }

    private function getBrands($filters, $store_id) {
        $_filters = $filters;
        $_filters['brands'] = [];
        $products_ids = $this->getProductWithFilter($_filters, $store_id)->get()->pluck('id');
        return ManufacturerProducts::has('manufacturer')->whereIn('product_id', $products_ids)->get()->pluck('manufacturer')->unique('id');
    }

    private function getPrices($filters, $store_id) {
        $_filters = $filters;
        $_filters['prices'] = [];
        $productsPrices = $this->getProductWithFilter($_filters, $store_id)->get()->pluck('product_price');
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
            'categories' => array_map('intval', array_filter(explode(',', ($query['category'] ?? '')), 'strlen')),
            'subcategories' => array_map('intval', array_filter(explode(',', ($query['subcategory'] ?? '')), 'strlen')),
            'brands' => array_map('intval', array_filter(explode(',', ($query['brands'] ?? '')), 'strlen')),
            'prices' => array_map('intval', array_filter(explode(',', ($query['prices'] ?? '')), 'strlen')),
            'is_hit' => isset($query['is_hit']) ? ($query['is_hit'] === 'true' ? 'true' : 'false') : 'false',
            'search' => isset($query['search']) ? $this->prepareSearchString($query['search']) : ''
        ];
    }

    private function getProductWithFilter($filters, $store_id) {
        return Product::ofTag($filters['search'])
            ->ofCategory($filters['categories'])
            ->ofSubcategory($filters['subcategories'])
            ->ofBrand($filters['brands'])
            ->ofPrice($filters['prices'])
            /*->inStock($store_id)*/
            ->isHit($filters['is_hit'])
            ->where('is_site_visible', true)
            ->groupBy('group_id')
            ->with(['attributes', 'attributes.attribute_name', /*'manufacturer',*/ /*'categories',*/ 'subcategory', /*'children',*/ 'price', 'product_images'])
            /*->with(['quantity' => function ($query) use ($store_id) {
                return $query->where('store_id', $store_id);
            }])*/;
    }

    private function getFilteredProducts($query, $store_id) {
        $filters = $this->getFilterParametrs($query, $store_id);
        return ProductsResource::collection($this->getProductWithFilter($filters, $store_id)->paginate(24));
    }


    public function getHeading(Request $request) {
        $query = $request->all();
        if (isset($query['category'])) {
            return ['heading' => Category::find($query['category'])->category_name];
        }
        if (isset($query['subcategory'])) {
            return ['heading' => Subcategory::find($query['subcategory'])->subcategory_name];
        }

        if (isset($query['search'])) {
            return ['heading' => "Результаты поиска по запросу: '" . $query['search'] . "'"];
        }
    }

    public function getProduct(Product $product) {
        return new \App\Http\Resources\shop\ProductResource($product);
    }

    public function groupProducts() {
        $products = Product::with('manufacturer')->get();
        $products = $products->map(function ($i) {
            $i['manufacturer_id'] = count($i['manufacturer']) ? $i['manufacturer']['id'] : null;
            unset($i['product_description']);
            return $i;
        });
        $products = $products->groupBy(['product_name', 'product_price', 'manufacturer_id']);

        $products->each(function ($price) {
            collect($price)->each(function ($manufacturer) {
                collect($manufacturer)->each(function ($product) {
                    $ids = collect($product)->pluck('id');
                    $group_id = $ids->first();
                    Product::where('id', $ids)->update([
                        'group_id' => $group_id
                    ]);
                });
            });
        });
    }
}
