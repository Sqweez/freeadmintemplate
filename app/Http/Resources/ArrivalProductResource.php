<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ArrivalProductResource extends JsonResource
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
            'attributes' => AttributeResource::collection($this->product->attributes),
            'id' => $this->product->id,
            'count' => $this->count,
            'product_name' => $this->product->product_name,
            'product_price' => $this->product->product_price,
            'purchase_price' => $this->purchase_price,
        ];
    }
}
