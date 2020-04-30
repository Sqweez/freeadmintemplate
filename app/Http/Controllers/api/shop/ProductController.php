<?php

namespace App\Http\Controllers\api\shop;

use App\Category;
use App\CategoryProduct;
use App\Http\Controllers\Controller;
use App\Http\Resources\shop\ProductsResource;
use App\Subcategory;
use App\SubcategoryProduct;
use App\Product;
use App\ManufacturerProducts;
use Illuminate\Http\Request;

class ProductController extends Controller {
    public function getProducts(Request $request) {
        $query = $request->all();
        $products = $this->getFilteredProducts($query);
        return $products;
    }

    public function filters(Request $request) {
        $query = $request->all();
        return $this->getFilters($query);
    }

    private function getBySearch($search) {
        //return ProductsResource::collection(Product::paginate(15));
    }

    private function getFilters($query) {
        $filters = $this->getFilterParametrs($query);
        $data = [
            'brands' => $this->getBrands($filters),
            'prices' => $this->getPrices($filters),
        ];
        return $this->convertFilters($data);
    }

    private function getBrands($filters) {
        $_filters = $filters;
        $_filters['brands'] = [];
        $products_ids = $this->getProductWithFilter($_filters)->get()->pluck('id');
        return ManufacturerProducts::with('manufacturer')->whereIn('product_id', $products_ids)->get()->pluck('manufacturer')->unique('id');
    }

    private function getPrices($filters) {
        $_filters = $filters;
        $_filters['prices'] = [];
        $productsPrices = $this->getProductWithFilter($_filters)->get()->pluck('product_price');
        return [
            'min_price' => $productsPrices->min(),
            'max_price' => $productsPrices->max()
        ];
    }

    private function convertFilters($filters) {
        $array = [];

        foreach ($filters as $key => $filter) {
            foreach ($filter as $item) {
                $array[$key][] = $item;
            }
        }

        return $array;
    }


    private function getFilterParametrs($query) {
        return [
            'categories' => array_map('intval', array_filter(explode(',', ($query['category'] ?? '')), 'strlen')),
            'subcategories' => array_map('intval', array_filter(explode(',', ($query['subcategory'] ?? '')), 'strlen')),
            'brands' => array_map('intval', array_filter(explode(',', ($query['brands'] ?? '')), 'strlen')),
            'prices' => array_map('intval', array_filter(explode(',', ($query['prices'] ?? '')), 'strlen')),
        ];
    }

    private function getProductWithFilter($filters) {
        return Product::ofCategory($filters['categories'])->ofSubcategory($filters['subcategories'])->ofBrand($filters['brands'])->ofPrice($filters['prices']);
    }

    private function getFilteredProducts($query) {
        $filters = $this->getFilterParametrs($query);
        return ProductsResource::collection($this->getProductWithFilter($filters)->paginate(24));
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
}
