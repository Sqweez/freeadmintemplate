<?php

namespace App\Http\Resources\Opt;

use App\v2\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;

/* @mixin Product */
class ProductResource extends JsonResource
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
            'isFavorite' => null,
            'subcategory' => $this->subcategory,
            'product_image' => null,
            'price' => $this->price,
            'original_price' => null,
            'has_stock' => false,
            'slug' => $this->getOptLink(),
            'quantity_type' => [
                'text' => null,
                'color' => null
            ],
        ];
    }
}
