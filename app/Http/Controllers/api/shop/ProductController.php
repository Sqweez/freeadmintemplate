<?php

namespace App\Http\Controllers\api\shop;

use App\Category;
use App\CategoryProduct;
use App\Http\Controllers\Controller;
use App\Http\Resources\shop\ProductsResource;
use App\Subcategory;
use Illuminate\Http\Request;

class ProductController extends Controller {
    public function getProducts(Request $request) {
        $query = $request->all();
        if (isset($query['search'])) {
            return $this->getBySearch($query['search']);
        }

        if (isset($query['category'])) {
            return $this->getByCategory($query['category']);
        }
    }

    private function getBySearch($search) {
        //return ProductsResource::collection(Product::paginate(15));
    }

    private function getByCategory($category) {
        return ProductsResource::collection(CategoryProduct::where('category_id', $category)->paginate(12));
    }

    public function getHeading(Request $request) {
        $query = $request->all();
        if (isset($query['category'])) {
            return Category::find($query['category'])->category_name;
        }
        if (isset($query['subcategory'])) {
            return Subcategory::find($query['subcategory'])->subcategory_name;
        }
    }
}
