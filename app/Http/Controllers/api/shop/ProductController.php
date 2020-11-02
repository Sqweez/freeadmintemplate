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
            ->inStock($store_id)
            ->isHit($filters['is_hit'])
            ->where('is_site_visible', true)
            ->groupBy('group_id')
            ->with(['attributes', 'manufacturer', 'categories', 'subcategories', 'children', 'quantity', 'price']);
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
        return new ProductResource($product);
    }

    public function groupProducts()
    {
        $products = Product::all();
        $products = $products->groupBy(['product_name', 'product_price']);

        foreach($products as $key => $product) {
            foreach ($product as $key2 => $item) {
                if (count ($item) > 1) {
                    $group_id = $item[0]['id'];
                    foreach ($item as $_i) {
                        $pr = Product::find($_i['id']);
                        $pr->update(['group_id' => $group_id]);
                    }
                }
            }
        }

    }
}
