<?php

namespace App\Http\Resources\Stocks;

use Illuminate\Http\Resources\Json\JsonResource;

class StockProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product_name' => $this->product->product_name,
            'attributes' => $this->product->attributes->pluck('attribute_value'),
            'manufacturer' => $this->product->manufacturer->manufacturer_name,
            'initial_price' => $this->product->product_price,
            'product_price' => ceil($this->product->product_price * (1 - $this->discount))
        ];
    }
}
