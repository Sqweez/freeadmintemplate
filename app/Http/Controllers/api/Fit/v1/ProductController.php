<?php

namespace App\Http\Controllers\api\Fit\v1;

use App\Http\Controllers\api\BaseApiController;
use App\Http\Resources\Fit\FitProductsResource;
use App\Models\FitUser;
use App\ProductBatch;
use App\Sale;
use App\v2\Models\ProductSku;
use Illuminate\Http\Request;

class ProductController extends BaseApiController
{
    public function index()
    {
        /* @var FitUser $user */
        $user = auth()->user();
        $store_id = $user->gym->store_id;
        $products = ProductSku::query()
            ->whereHas('batches', function ($query) use ($store_id) {
                return $query
                    ->where('store_id', $store_id)
                    ->where('quantity', '>', 0);
            })
            ->with(['batches' => function ($query) use ($store_id) {
                return $query
                    ->where('store_id', $store_id)
                    ->where('quantity', '>', 0)
                    ->select(['id', 'quantity', 'product_id']);
            }])
            ->with('product.attributes')
            ->with('attributes')
            ->with('product.category')
            ->with('product.manufacturer')
            ->get();

        return FitProductsResource::collection(
            $products
        );
    }

    public function sale(Request $request)
    {
      /*  const payload = {
            id: item.id,
            client_id: this.searchedClient.id,
            quantity: __hardcoded(1),
            price: item.price,
        };*/
        $user = auth()->user();
        $store_id = $user->gym->store_id;
        $data = $request->all();
        $hasBatches = ProductBatch::query()
            ->where('store_id', $store_id)
            ->where('quantity', '>', 0)
            ->where('product_id', $data['id'])
            ->count();

        if (!$hasBatches) {
            return $this->respondError(['Недостаточно товара на складе']);
        }

        $sale = Sale::create([
            'user_id' => __hardcoded(32),
            'store_id' => $store_id,
            'fit_client_id' => $data['client_id'],
        ]);

        $batch = ProductBatch::query()
            ->where('store_id', $store_id)
            ->where('quantity', '>', 0)
            ->where('product_id', $data['id'])
            ->first();

        $sale->products()->create([
            'product_batch_id' => $batch->id,
            'product_id' => $data['id'],
            'purchase_price' => $batch->purchase_price,
            'product_price' => $data['price'],
            'discount' => 0
        ]);

        $batch->decrement('quantity');

        return $this->respondSuccess();
    }
}
