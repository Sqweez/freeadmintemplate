<?php

namespace App\Http\Controllers\api\v3;

use App\Http\Controllers\api\BaseApiController;
use App\v2\Models\ProductSku;
use Illuminate\Http\Request;

class KaspiBotController extends BaseApiController
{
    public function updatePrice(ProductSku $sku, Request $request)
    {
        $price = $request->get('price', null);
        if (!$price) {
            return $this->respondError([
                'message' => 'Не передан параметр стоимость'
            ]);
        }
        return $sku;
    }
}
