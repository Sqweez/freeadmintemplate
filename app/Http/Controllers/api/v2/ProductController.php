<?php

namespace App\Http\Controllers\api\v2;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function search(Request $request) {
        $search = $request->get('search');
        $products = Product::ofTag($this->prepareSearchString($search))->orderBy('group_id')
            ->with(['attributes', 'manufacturer', 'categories', 'subcategories', 'quantity', 'price', 'tag'])
            ->get();

        return ProductResource::collection($products);
    }

    private function prepareSearchString($search) {
        return "%" . str_replace(' ', '%', $search) . "%";
    }
}
