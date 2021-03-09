<?php

namespace App\Http\Resources\v2\Order;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderProductsResource extends JsonResource
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
            'product_name' => $this->product['product_name'],
            'manufacturer' => $this->product['manufacturer']['manufacturer_name'],
            'product_price' => $this->product['product_price'],
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
