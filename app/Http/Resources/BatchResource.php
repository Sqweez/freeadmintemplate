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
            'attributes' => AttributeResource::collection($this->product->attributes),
            'manufacturer' => $this->product->manufacturer[0]->manufacturer_name ?? '',
            'product_price' => $this->product->product_price,
            'count' => $this->count,
        ];
    }
}
