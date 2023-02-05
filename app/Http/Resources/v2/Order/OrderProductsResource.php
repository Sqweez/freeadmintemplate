<?php

namespace App\Http\Resources\v2\Order;

use App\OrderProduct;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/* @mixin OrderProduct */
class OrderProductsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array {
        return [
            'product_name' => $this->product['product_name'],
            'manufacturer' => $this->product['manufacturer']['manufacturer_name'],
            'product_price' => ($this->product['product_price'] * ((100 - intval($this->product['discount'])) / 100)),
            'category' => $this->product['category']['category_name'],
            'subcategory' => $this->product['subcategory']['subcategory_name'],
            'product_sku_id' => $this->product['id'],
            'attributes' => collect($this->product['product']['attributes'])->merge($this->product['attributes']),
            'count' => $this->count ?? 0,
            'store' => $this->batch->store,
            'order_item_id' => $this->id,
            'id' => $this->id,
        ];
    }
}
