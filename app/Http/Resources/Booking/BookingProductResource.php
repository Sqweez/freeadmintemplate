<?php

namespace App\Http\Resources\Booking;

use App\v2\Models\BookingProduct;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class BookingProduct
 * @mixin BookingProduct
 * */

class BookingProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        //return parent::toArray($request);

        return [
            'id' => $this->id,
            'product_name' => $this->product->product_name,
            'product_price' => $this->product->product_price,
            'count' => $this->count,
            'manufacturer' => $this->product->product->manufacturer->manufacturer_name,
            'attributes' => collect($this->product->product->attributes)
                ->mergeRecursive($this->product->attributes)
                ->pluck('attribute_value')
        ];
    }
}
