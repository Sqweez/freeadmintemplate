<?php

namespace App\Http\Resources;

use App\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class BatchResource extends JsonResource
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
            'batch_id' => $this->batch_id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->product_name,
            'attributes' => collect($this->product->attributes)->map(function ($attribute) {
                return [
                    'attribute_value' => $attribute->attribute_value,
                    'attribute_name' => $attribute->attribute_name->attribute_name,
                ];
            })->merge(collect($this->product->product->attributes)->map(function ($attribute) {
                return [
                    'attribute_value' => $attribute->attribute_value,
                    'attribute_name' => $attribute->attribute_name->attribute_name,
                ];
            })),
            'manufacturer' => $this->product->manufacturer,
            'product_price' => $this->product->product_price,
            'count' => $this->count,
        ];
    }
}
