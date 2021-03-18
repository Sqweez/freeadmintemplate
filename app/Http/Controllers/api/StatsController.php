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
        $store = $request->has('store') ? $request->get('store') : 1;
        $time = $request->get('time') ? $request->get('time') : 'last_30_days';
        $today = Carbon::now();

        $dateStart = Carbon::now()->subDays(30);

        switch ($time) {
            case 'last_30_days':
                $dateStart = Carbon::now()->subDays(30);
                break;
            case 'last_7_days':
                $dateStart = Carbon::now()->subDays(7);
                break;
            case 'all_time':
                $dateStart = Carbon::createFromTimestamp(0);
                break;
            default:
                break;
        }

        $sales = $store == -1 ?
            Sale::whereDate('created_at', '<', $today)
            ->whereDate('created_at', '>', $dateStart)
            ->where('discount', '<', 100)
            ->with('products')
            ->get() :
            Sale::whereDate('created_at', '<', $today)
                ->whereDate('created_at', '>', $dateStart)
                ->where('discount', '<', 100)
                ->where('store_id', $store)
                ->with('products')
                ->get();

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

        $worstProducts = $categories->map(function ($items, $key) use ($sales) {
            return collect($items)->map(function ($i) use ($sales) {
                return [
                    'product_id' => $i['product_id'],
                    'count' => $sales[$i['product_id']],
                ];
            })->sortByDesc('count')->values()->chunk(3)->last();
        });

        $mvpProducts = $mvpProducts->map(function ($items) {
            return $items->map(function ($item) {
                return [
                    'product' => new ProductResource(Product::find($item['product_id'])),
                    'count' => $item['count']
                ];
            });
        });

        $worstProducts = $worstProducts->map(function ($items) {
            return $items->map(function ($item) {
                return [
                    'product' => new ProductResource(Product::find($item['product_id'])),
                    'count' => $item['count']
                ];
            });
        });


        return [
            'best_products' => $mvpProducts,
            'worst_products' => $worstProducts
        ];
    }
}
