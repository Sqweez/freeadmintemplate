<?php

namespace App\Http\Resources\Opt;

use App\v2\Models\WholesaleOrderProduct;
use Illuminate\Http\Resources\Json\JsonResource;

/* @mixin WholesaleOrderProduct */
class OrderProductsHistoryResource extends JsonResource
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
            'product_image' => $this->product->product->retrieveProductThumb(),
            'product_name' => $this->product->product->product_name,
            'manufacturer' => $this->product->product->manufacturer->manufacturer_name,
        ];
    }
}
