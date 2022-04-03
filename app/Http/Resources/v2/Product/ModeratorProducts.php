<?php

namespace App\Http\Resources\v2\Product;

use Illuminate\Http\Resources\Json\JsonResource;

class ModeratorProducts extends JsonResource
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
            'id' => $this->id,
            'product_name' => $this->product_name,
            'category' => $this->category,
            'subcategory_id' => $this->product->subcategory_id,
            'manufacturer' => $this->manufacturer,
            'attributes' => collect($this->attributes)->map(function ($attribute) {
                return [
                    'attribute_value' => $attribute->attribute_value,
                    'attribute_name' => $attribute->attribute_name->attribute_name,
                ];
            })->merge(collect($this->product->attributes)->map(function ($attribute) {
                return [
                    'attribute_value' => $attribute->attribute_value,
                    'attribute_name' => $attribute->attribute_name->attribute_name,
                ];
            })),
            'product_barcode' => $this->product_barcode,
            'product_price' => $this->product_price,
            'quantity' => 0,
            'sku_can_be_created' => !!$this->grouping_attribute_id,
            'grouping_attribute_id' => $this->grouping_attribute_id,
            'product_id' => $this->product_id,
            'product_description' => $this->product_description,
            'product_images' => $this->product->product_images,
            'tags' => $this->product->tags,
        ];
    }
}
