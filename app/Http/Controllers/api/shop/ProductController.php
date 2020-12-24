<?php

namespace App\Http\Controllers\api\shop;

use App\Category;
use App\Http\Controllers\Controller;
use App\Http\Resources\shop\ProductResource;
use App\Http\Resources\shop\ProductsResource;
use App\Subcategory;
use App\Product;
use App\ManufacturerProducts;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use \Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Support\Str;

class ProductController extends Controller {

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
            Product::FILTER_CATEGORIES => array_map('intval', array_filter(explode(',', ($query[Product::FILTER_CATEGORIES] ?? '')), 'strlen')),
            Product::FILTER_SUBCATEGORIES => array_map('intval', array_filter(explode(',', ($query[Product::FILTER_SUBCATEGORIES] ?? '')), 'strlen')),
            Product::FILTER_BRANDS => array_map('intval', array_filter(explode(',', ($query[Product::FILTER_BRANDS] ?? '')), 'strlen')),
            Product::FILTER_PRICES => array_map('intval', array_filter(explode(',', ($query[Product::FILTER_PRICES] ?? '')), 'strlen')),
            Product::FILTER_IS_HIT => isset($query[Product::FILTER_IS_HIT]) ? ($query[Product::FILTER_IS_HIT] === 'true' ? 'true' : 'false') : 'false',
            Product::FILTER_SEARCH => isset($query[Product::FILTER_SEARCH]) ? $this->prepareSearchString($query[Product::FILTER_SEARCH]) : ''
        ];
    }

    private function getProductWithFilter($filters, $store_id) {
        return Product::ofTag($filters[Product::FILTER_SEARCH])
            ->ofCategory($filters[Product::FILTER_CATEGORIES])
            ->ofSubcategory($filters[Product::FILTER_SUBCATEGORIES])
            ->ofBrand($filters[Product::FILTER_BRANDS])
            ->ofPrice($filters[Product::FILTER_PRICES])
            ->inStock($store_id)
            ->isHit($filters[Product::FILTER_IS_HIT])
            ->where('is_site_visible', true)
            ->groupBy('group_id')
            ->with(['attributes', 'attributes.attribute_name', /*'manufacturer',*/ /*'categories',*/ 'subcategory', /*'children',*/ 'price', 'product_images']);
    }

    private function getFilteredProducts($query, $store_id) {
        $filters = $this->getFilterParametrs($query, $store_id);
        return ProductsResource::collection($this->getProductWithFilter($filters, $store_id)->paginate(24));
    }


    public function getProduct(Product $product) {
        return new ProductResource($product);
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
