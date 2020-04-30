<?php

namespace App\Http\Controllers\api\shop;

use App\Category;
use App\CategoryProduct;
use App\Http\Controllers\Controller;
use App\Http\Resources\shop\ProductsResource;
use App\Subcategory;
use App\SubcategoryProduct;
use Illuminate\Http\Request;

class ProductController extends Controller {
    public function getProducts(Request $request) {
        $query = $request->all();
        if (isset($query['subcategory'])) {
            $products = $this->getBySubcategory($query['subcategory']);
            return $products;
        }
        $products = $this->getByCategory($query['category']);
        return $products;
    }

    private function getBySearch($search) {
        //return ProductsResource::collection(Product::paginate(15));
    }

    private function getByCategory($category) {
        $categories = explode(',', $category);
        return ProductsResource::collection(CategoryProduct::whereIn('category_id', $categories)->paginate(12));
    }

    private function getBySubcategory($subcategory) {
        $subcategories = explode(',', $subcategory);
        return ProductsResource::collection(SubcategoryProduct::whereIn('subcategory_id', $subcategories)->paginate(12));
    }

    private function getFilters($query) {

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
