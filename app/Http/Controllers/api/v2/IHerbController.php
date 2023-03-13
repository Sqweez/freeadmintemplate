<?php

namespace App\Http\Controllers\api\v2;

use App\Actions\Documents\CreateIHerbPriceListAction;
use App\Http\Controllers\api\WaybillController;
use App\Http\Controllers\Controller;
use App\Http\Resources\v2\Product\IHerbProductsResource;
use App\v2\Models\ProductSku;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IHerbController extends Controller
{
    public function getPriceList(Request $request, CreateIHerbPriceListAction $action): JsonResponse {
        $sku = ProductSku::query()
            ->whereHas('product', function ($q) {
                return $q->where('is_iherb', true);
            })
            ->with(ProductSku::PRODUCT_SKU_WITH_ADMIN_LIST)
            ->with('batches')
            ->get();

        $productsCollection = collect(
            IHerbProductsResource::collection($sku)
                ->toArray($request)
        )
            ->filter(function ($item) {
                return $item['total_quantity'] > 0;
            })
            ->values()
            ->toArray();

        return $action->handle($productsCollection);
    }
}
