<?php

namespace App\Http\Controllers;

use App\CategoryProduct;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ReportResource;
use App\Http\Resources\TransferResource;
use App\Product;
use App\ProductBatch;
use App\ProductQuantity;
use App\Sale;
use App\Transfer;
use Carbon\Carbon;
use Barryvdh\Debugbar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    public function index(Request $request) {
       /* $batches = ProductBatch::where('quantity', '>', 0)->get();
        $batches = collect($batches)->groupBy('product_id');
        $batches = collect($batches)->map(function ($i) {
            return $i->groupBy('store_id')->map(function ($r) {
                return $r->sum('quantity');
            });
        });

        $array = [];

        return $batches->map(function ($item, $key) {
            $product_id = $key;
            $item->map(function ($_item, $_key) use ($product_id) {
                $store_id = $_key;
                DB::table('product_quantities')->insert(
                    [
                        'product_id' => $product_id,
                        'store_id' => $store_id,
                        'quantity' => $_item
                    ]
                );
            });
        });*/
        $mode = $request->get('mode');
        $transfers = Transfer::where('is_confirmed', !($mode === 'current'))
            ->with(['parent_store', 'child_store'])
            ->with(['batches' => function ($query) {
                return $query
                    ->leftJoin('product_batches', 'product_batches.id', '=', 'transfer_batches.batch_id')
                    ->leftJoin('products', 'products.id', '=', 'transfer_batches.product_id')
                    ->select('transfer_batches.id', 'product_batches.purchase_price as purchase_price', 'products.product_price as product_price');
            }])
            ->select('id', 'parent_store_id', 'child_store_id', 'user_id', 'photos')
            ->get();
        return view('test', [
            'transers' => $transfers->toArray()
        ]);
    }
}
