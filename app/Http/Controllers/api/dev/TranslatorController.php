<?php

namespace App\Http\Controllers\api\dev;

use App\Http\Controllers\Controller;
use App\v2\Models\Product;
use Illuminate\Http\Request;

class TranslatorController extends Controller
{
    public function getProductsDescriptions(Request $request)
    {
        $products = Product::query()
            ->whereNull('product_description_kaz')
            ->select(['id', 'product_description', 'product_description_kaz'])
            ->get();

        return $products->toJson();
    }
}
