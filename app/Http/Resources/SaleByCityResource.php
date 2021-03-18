<?php

namespace App\Http\Resources;

use App\Sale;
use App\v2\Models\Product;
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

        $balance = intval($this->balance);

        $kaspi_red = intval($this->kaspi_red);

        return [
            'id' => $this->id,
            'store_id' => intval($this->store_id),
            'total_cost' => $this->getFinalPrice($discount, $cost, $balance, $kaspi_red)
        ];
    }

    private function getFinalPrice($discount, $price, $balance, $kaspi_red) {
        return intval((($price - $price * $discount / 100) - $price * Sale::KASPI_RED_PERCENT * $kaspi_red) - $balance);
    }
}
