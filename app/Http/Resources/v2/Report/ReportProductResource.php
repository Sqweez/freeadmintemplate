<?php

namespace App\Http\Resources\v2\Report;

use App\SaleProduct;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @mixin SaleProduct
     * @return array
     */
    public function toArray($request)
    {
        return [
            'product_id' => $this->product->id,
            'product_name' => $this->product->product_name,
            'attributes' => collect($this->product->attributes)
                ->map(function ($attribute) {
                    return $attribute->attribute_value;
                })
                ->merge(collect($this->product->product->attributes ?? [])
                    ->map(function ($attribute) {
                        return $attribute->attribute_value;
            })),
            '_attributes' => collect($this->product->attributes)->merge(collect($this->product->product->attributes ?? [])),
            'manufacturer' => $this->product->manufacturer,
            'product_price' => $this->product_price,
          /*  'count' => $this->count,*/
            'discount' => $this->discount
        ];
    }
}
