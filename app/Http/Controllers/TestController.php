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
        $clients = Client::with('sales.sale.store.city_name')->with('city:name')->get();
        return $clients->filter(function ($client) {
            return count($client['sales']) > 0;
        })->values()->map(function ($client) {
            $sale = collect($client['sales'])->map(function ($sale) {
                return $sale['sale']['store']['city_name']['name'];
            })->unique();
            unset($client['sales']);
            $client['cities'] = $sale;
            return $client;
        })->map(function ($client) {
            return [
                'Имя' => $client['client_name'],
                'Телефон' => $client['client_phone'],
                'Указанный город' => $client['city']['name'],
                'Города, где были продажи' => collect($client['cities'])->join(', ')
            ];
        });
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
        $saleQuery = Sale::query();
        $saleQuery = $saleQuery->reportDate(['2021-01-26', '2021-02-26'])->reportSupplier(25);
        return view('test', [
            'test' => $saleQuery->get()
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
