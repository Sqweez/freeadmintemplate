<?php

namespace App\Http\Resources\shop;

use App\CartProduct;
use App\ProductBatch;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request) {

        $_products = $this->filterProduct($this->products, $this->store_id, $this->id);

        return [
            'id' => $this->id,
            'user_token' => $this->user_token,
            'products' => CartProductResource::collection($_products)
        ];
    }

    private function filterProduct($products, $store_id, $cart_id) {
        return collect($products)->map(function ($product) use ($store_id, $cart_id) {
            $currentCount = ProductBatch::ofProduct($product['product_id'])->ofStore($store_id)->sum('quantity');
            if ($product['count'] > $currentCount) {
                $product['count'] = $currentCount;
                CartProduct::Cart($cart_id)->Product($product['product_id'])->update(['count' => $product['count']]);
            }
            return $product;
        });
    }

}
