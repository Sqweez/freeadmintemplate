<?php

namespace App\Http\Controllers\api;

use App\CategoryProduct;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Product;
use App\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function getMVPProducts(Request $request) {
        $today = Carbon::now();
        $monthStart = Carbon::now()->subDays(30);
        $sales = Sale::whereDate('created_at', '<', $today)->whereDate('created_at', '>', $monthStart)->with('products')->get();
        $sales = $sales->pluck('products')->flatten()->groupBy('product_id');
        $sales = $sales->map(function ($i) {
            return collect($i)->count();
        });
        $categories = CategoryProduct::whereIn('product_id', $sales->keys())->get()->groupBy('category_id');
        $mvpProducts = $categories->map(function ($items, $key) use ($sales) {
            return collect($items)->map(function ($i) use ($sales) {
                return [
                    'product_id' => $i['product_id'],
                    'count' => $sales[$i['product_id']],
                ];
            })->sortByDesc('count')->values()->chunk(3)->first();
        });

        $mvpProducts = $mvpProducts->map(function ($items) {
            return $items->map(function ($item) {
                return [
                    'product' => new ProductResource(Product::find($item['product_id'])),
                    'count' => $item['count']
                ];
            });
        });

        return [
            'by_category' => $mvpProducts
        ];
    }
}
