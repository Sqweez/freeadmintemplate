<?php

namespace App\Http\Resources\v2\Sku;

use Illuminate\Http\Resources\Json\JsonResource;

class SkuResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $grouping_attribute_id = $this->grouping_attribute_id;

        return [
            'id' => $this->id,
            'price' => $this->product_price,
            'product_name' => $this->product_name,
            'product_description' => $this->product_description,
            'product_price' => $this->product_price,
            //'product_barcode' => $this->product_barcode,
            'is_hit' => $this->is_hit,
            'is_site_visible' => $this->is_site_visible,
            'category' => $this->category,
            'manufacturer' => $this->manufacturer,
            'discount_price' => $this->discount_price,
            'children' => $this->children->toArray($request)
        ];
    }
}
