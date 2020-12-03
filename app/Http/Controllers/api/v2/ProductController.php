<?php

namespace App\Http\Controllers\api\v2;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Product;
use App\ProductBatch;
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

    public function changeCount($id, Request $request) {
        $batchQuery = ProductBatch::query()->where('store_id', $request->get('store_id'))->where('product_id', $id);
        if (intval($request->get('increment')) === -1) {
            $batchQuery->where('quantity', '>', 0);
        }

        $batch = $batchQuery->orderBy('created_at', 'desc')->first();
        if (!$batch) {
            return response()->json(['message' => 'По данному товару не было поставок!'], 500);
        }

        $batch->quantity += $request->get('increment');

        $batch->save();

        return $batch;
    }
}
