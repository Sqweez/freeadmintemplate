<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductSaleResource extends JsonResource
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
            'product_id' => $this->product_id,
            'product_name' => $this->products->product_name,
            'purchase_price' => $this->purchase_price,
            'product_price' => $this->product_price,
            'attributes' => AttributeResource::collection($this->products->attributes),
            'manufacturer' => $this->products->manufacturer->first()->manufacturer_name ?? '',
        ];
    }
}
