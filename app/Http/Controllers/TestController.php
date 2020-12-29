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

//        $sales =  Sale::where('discount', '!=', 0)->cursor();
//        foreach ($sales as $sale) {
//            SaleProduct::whereSaleId($sale['id'])->update([
//                'discount' => $sale['discount']
//            ]);
//        }
        /*return Sale::where('discount', '!=', 0)->whereHas('products', function ($query) {
            return $query->where('discount', 0);
        })->chunk(100, function ($sales) {
            $sales->each(function ($sale) {
                SaleProduct::whereKey($sale['id'])->update([
                    'discount' => $sale['discount']
                ]);
            });
        });*/

        $filters = [
            'category' => [],
            'subcategory' => [],
            'brands' => [],
            'prices' => [],
            'is_hit' => 'false',
            'search' => '%iso%',
        ];

        $output =  [
            'brands' => ($this->getBrands($filters, 1)),
            //'prices' => $this->getPrices($filters, $store_id),
        ];

        /*$productQuery = Product::query()->whereIsSiteVisible(true);

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

        $productQuery->inStock(1);

        $productQuery->with(['subcategory', 'attributes', 'product_images']);


        $products =  \App\Http\Resources\shop\ProductsResource::collection($productQuery->paginate(24));*/



        return view('test', [
            'product' => $output
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
