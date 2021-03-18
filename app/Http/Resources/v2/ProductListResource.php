<?php

namespace App\Http\Resources\v2;

use App\Http\Controllers\Services\ProductService;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductListResource extends JsonResource
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
            'attributes' => collect($this->attributes)->map(function ($attribute) {
                return [
                    'attribute_value' => $attribute->attribute_value,
                    'attribute_name' => $attribute->attribute_name->attribute_name,
                ];
            })->concat($this->product->attributes->map(function ($attribute) {
                return [
                    'attribute_value' => $attribute->attribute_value,
                    'attribute_name' => $attribute->attribute_name->attribute_name,
                ];
            })),
            'manufacturer' => $this->manufacturer,
            'product_price' => $this->product_price,
            'quantity' => 0,
            'product_barcode' => $this->product_barcode,
            'parent_id' => $this->product->id,
           /* 'children' => $this->children->map(function ($i) {
                return [
                    'attributes' => collect($i['attributes'])->map(function ($attribute) {
                        return [
                            'attribute_value' => $attribute->attribute_value,
                            'attribute_name' => $attribute->attribute_name->attribute_name,
                        ];
                    })->concat(collect($this->attributes)->map(function ($attribute) {
                        return [
                            'attribute_value' => $attribute->attribute_value,
                            'attribute_name' => $attribute->attribute_name->attribute_name,
                        ];
                    })),
                    'id' => $i['id'],
                    'parent_id' => $i['product_id'],
                    'product_barcode' => $i['product_barcode'],
                ];
            })*/
        ];
    }
}
