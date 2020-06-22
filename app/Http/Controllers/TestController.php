<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Product;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index() {
        $products = ProductResource::collection(
            Product::orderBy('group_id')
                ->with(['categories', 'subcategories', 'attributes', 'manufacturer'])
                ->get()
        );
        return view('test', compact('products'));
    }
}
