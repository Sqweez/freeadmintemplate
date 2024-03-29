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
        $payload =  [
            'id' => $this->id,
            'product_name' => $this->product_name,
            'isFavorite' => null,
            'subcategory' => $this->subcategory,
            'product_image' => $this->product_thumbs,
            'has_stock' => false,
            'slug' => $this->getOptLink(),
            'brand_id' => $this->manufacturer_id,
            'quantity_type' => [
                'text' => null,
                'color' => null
            ],
        ];

        return array_merge($payload, $this->getPrice());
    }

    private function getPrice(): array
    {
        $prices = $this->wholesale_prices->first();
        return [
            'currencySign' => optional($prices)->currency->unicode_symbol,
            'original_price' => null,
            'price' => optional($prices)->price,
            'isPriceSet' => !empty($prices)
        ];
    }
}
