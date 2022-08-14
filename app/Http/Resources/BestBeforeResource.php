<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class BestBeforeResource extends JsonResource
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
            'product_name' => $this->sku->product->product_name,
            'manufacturer' => $this->sku->product->manufacturer->manufacturer_name,
            'best_before' => Carbon::parse($this->best_before)->format('d.m.Y'),
            'is_expired' => $this->is_expired,
            'days_to_expire' => $this->days_to_expire,
            'attributes' => collect($this->sku->attributes)->map(function ($attribute) {
                return [
                    'attribute_value' => $attribute->attribute_value,
                    'attribute_name' => $attribute->attribute_name->attribute_name,
                ];
            })->merge(collect($this->sku->product->attributes)->map(function ($attribute) {
                return [
                    'attribute_value' => $attribute->attribute_value,
                    'attribute_name' => $attribute->attribute_name->attribute_name,
                ];
            }))->pluck('attribute_value')->join('|'),
            'product_price' => $this->sku->product_price,
            'store' => $this->store,
        ];
    }
}
