<?php

namespace App\Http\Resources\v2\Product;

use App\v2\Models\ProductSku;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ProductSku
 */
class ProductsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'product_name' => $this->product_name,
            'category' => $this->category,
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
        ];
        /*return [
            'id' => intval($this->id),
            'product_name' => $this->product_name,
            'category' => $this->category,
            'attributes' => collect($this->attributes)->map(function ($attribute) {
                return [
                    'attribute_value' => $attribute->attribute_value,
                    'attribute_name' => $attribute->attribute_name->attribute_name,
                ];
            }),
            'manufacturer' => $this->manufacturer,
            'product_price' => $this->product_price,
            'product_barcode' => $this->product_barcode,
            'group_id' => $this->group_id,
            'quantity' => 0,
        ];*/
    }
}
