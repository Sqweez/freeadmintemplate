<?php

namespace App\Http\Resources\Opt;

use App\v2\Models\UserCartItem;
use Illuminate\Http\Resources\Json\JsonResource;

/* @mixin UserCartItem */
class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'product_sku_id' => $this->product_id,
            'product_id' => $this->product->product_id,
            'product_image' => $this->product->product->retrieveProductThumb(),
            'product_name' => $this->product->product->product_name,
            'product_sub_name' => $this->product->product->attributes->pluck('attribute_value')->join(' '),
            'quantity' => $this->product->batches->sum('quantity'),
            'count' => $this->count,
            'type' => $this->product->attributes->pluck('attribute_value')->join(' ') ?: '-',
            'price' => $this->getPrice(),
            'id' => $this->id,
            'cart_id' => $this->cart_id,
            'link' => $this->product->product->getOptLink(),
            'is_editing_blocked' => $this->getPrice() === 0,
            'base_price' => $this->getBasePrice(),
            'manufacturer' => $this->product->product->manufacturer->manufacturer_name
        ];
    }
}
