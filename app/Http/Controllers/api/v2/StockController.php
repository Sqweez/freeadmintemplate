<?php

namespace App\Http\Controllers\api\v2;

use App\Http\Controllers\Controller;
use App\Http\Resources\Stocks\StockResource;
use App\v2\Models\Stock;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index(Request $request) {
        return StockResource::collection(Stock::with([
            'products', 'products.product',
            'products.product.attributes', 'products.product.manufacturer'
        ])->get());
    }

    public function getShopActiveStock(Request $request) {
        return Stock::active()->first();
    }

    public function store(Request $request) {
        $stock = Stock::create([
            'discount' => $request->get('discount'),
            'title' => $request->get('title'),
            'started_at' => $request->get('started_at'),
            'finished_at' => $request->get('finished_at')
        ]);

        $products = collect($request->get('products'))->map(function ($product) {
            return [
                'product_id' => $product
            ];
        });

        $stock->products()->createMany($products);
    }

    public function update(Stock $stock, Request $request) {
        $stock->update($request->all());
        return new StockResource(Stock::query()->find($stock->id));
    }

    public function destroy($id) {
        $stock = Stock::findOrFail($id);
        $stock->products()->delete();
        $stock->delete();
    }
}
