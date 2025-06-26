<?php

namespace App\Http\Controllers\api\v3;

use App\Http\Controllers\api\BaseApiController;
use App\v2\Models\ProductSku;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use LaravelIdea\Helper\App\_IH_ProductBatch_C;

class KaspiBotController extends BaseApiController
{
    public function updatePrice(ProductSku $sku, Request $request)
    {
        $authToken = $request->header('X-BOT-AUTH-TOKEN');
        if ($authToken !== config('app.bot_auth_token')) {
            return $this->respondError([
                'message' => 'Unauthorized'
            ], 401);
        }
        $price = $request->get('price');
        if (!$price) {
            return $this->respondError([
                'message' => 'Price is required.'
            ]);
        }
        $product = $sku->product;
        $kaspiPrice = $product->kaspi_price()
            ->where(
                'kaspi_entity_id',
                $request->get('entity_id', 1)
            )
            ->first();

        if (!$kaspiPrice) {
            return $this->respondError([
                'message' => 'Kaspi price is not set'
            ]);
        }
        $kaspiPrice->update(['price' => $price]);
        $batches = $sku->batches()->where('quantity', '>', 0)->with('store.city_name')->get();
        $batchesCity = $batches->groupBy('store.city_name.name');
        return $batchesCity->map(function ($batches, $city) {
            return [
                'name' => $city,
                'quantity' => $batches->sum('quantity'),
            ];
        })->values();
    }
}
