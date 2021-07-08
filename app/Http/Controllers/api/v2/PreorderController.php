<?php

namespace App\Http\Controllers\api\v2;

use App\Http\Controllers\Controller;
use App\Http\Resources\Preorder\PreodersListResource;
use App\Http\Resources\Preorder\PreorderReportResource;
use App\v2\Models\Preorder;
use App\v2\Models\PreorderProduct;
use Illuminate\Http\Request;

class PreorderController extends Controller
{
    public function index(Request $request) {
        $user_id = $request->get('user_id', null);
        $preorderQuery = Preorder::query();
        if ($user_id !== null) {
            $preorderQuery = $preorderQuery->whereUserId($user_id);
        }
        return PreodersListResource::collection($preorderQuery->with(['client', 'user', 'store','products.product', 'products'])
            ->with(['products.product.product:id,product_name,manufacturer_id'])
            ->with(['products.product.product.manufacturer', 'products.product.product.attributes', 'products.product.attributes'])
            ->get()
        );
    }

    public function store(Request $request) {
        $products = $request->get('products');
        $preorder = $request->except(['products']);
        $preorder = Preorder::create($preorder);
        foreach ($products as $product) {
            for ($i = 0; $i < $product['count']; $i++) {
                PreorderProduct::create([
                    'product_id' => $product['id'],
                    'preorder_id' => $preorder->id
                ]);
            }
        }

        return $preorder;
    }

    public function cancelPreOrder(Preorder $preorder) {
        $preorder->update(['status' => -1]);
    }

    public function getPreOrderReport(Request $request) {
        $start = $request->get('start');
        $finish = $request->get('finish');
        $user_id = $request->get('user_id', null);
        $preorderQuery = Preorder::query();
        if ($user_id !== null) {
            $preorderQuery = $preorderQuery->whereUserId($user_id);
        }
        return PreorderReportResource::collection(
            $preorderQuery->with(['client', 'user', 'store','products.product', 'products'])
                ->whereDate('created_at', '>=', $start)
                ->whereDate('created_at', '<=', $finish)
                ->where('status', '!=', -1)
                ->with(['products.product.product:id,product_name,manufacturer_id'])
                ->with(['products.product.product.manufacturer', 'products.product.product.attributes', 'products.product.attributes'])
                ->get()
        );
    }
}
