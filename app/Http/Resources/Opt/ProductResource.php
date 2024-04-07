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
        $quantity = $this->batches->sum('quantity');

        $payload =  [
            'id' => $this->id,
            'product_name' => trim(
                sprintf("%s %s", $this->product_name, $this->attributes->pluck('attribute_value')->join(','))
            ),
            'subcategory' => $this->subcategory,
            'product_image' => $this->retrieveProductThumb(),
            'has_stock' => false,
            'slug' => $this->getOptLink(),
            'brand_id' => $this->manufacturer_id,
            'quantity_type' => $this->getWholesaleQuantityType($quantity),
            'quantity' => $quantity,
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
