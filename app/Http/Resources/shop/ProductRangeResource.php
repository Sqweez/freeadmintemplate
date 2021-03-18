<?php

namespace App\Http\Resources\shop;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductRangeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $store_id = $request->get('store_id');

        return [
            'id' => $this->id,
            'product_taste' => $this->getTaste($this->attributes),
            'quantity' => $this->quantity->where('store_id', $store_id)->sum('quantity'),
        ];
    }

    private function getTaste($attributes) {
        return $attributes->filter(function ($i) {
            return $i['attribute_id'] == 1;
        })->first()['attribute_value'] ?? '';
    }
}
