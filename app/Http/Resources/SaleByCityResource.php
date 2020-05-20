<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SaleByCityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $cost = intval($this->products->sum('product_price'));

        $discount = intval($this->discount);

        return [
            'id' => $this->id,
            'store_id' => intval($this->store_id),
            'total_cost' => $this->getFinalPrice($discount, $cost)
        ];
    }

    private function getFinalPrice($discount, $price) {
        return intval($price - $price * $discount / 100);
    }
}
