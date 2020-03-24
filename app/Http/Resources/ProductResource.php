<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request) {
        return [
            'id' => $this->id,
            'product_name' => $this->product_name,
            'product_description' => $this->product_description,
            'categories' => $this->categories,
            'subcategories' => $this->subcategories,
            'attributes' => AttributeResource::collection($this->attributes),
            'manufacturer' => $this->manufacturer->first()->manufacturer_name ?? '',
            'manufacturer_id' => $this->manufacturer->first()->id ?? '',
            'product_price' => $this->product_price,
            'quantity' => $this->quantity,
            'product_barcode' => $this->product_barcode,
            'group_id' => $this->group_id,
        ];
    }
}
