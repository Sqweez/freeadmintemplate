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

        return [
            'id' => $this->id,
            'user_token' => $this->user_token,
            'products' => collect(CartProductResource::collection($this->products->filter(function($q) {return $q['product'];})))
        ];
    }

}
