<?php

namespace App\Http\Resources\Opt;

use App\v2\Models\Product;
use App\v2\Models\WholesalePrice;
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
            //'brand_id' => $this->manufacturer_id,
            'quantity_type' => $this->getWholesaleQuantityType($quantity),
            //'quantity' => $quantity,
            //'quantity_merged' => $this->quantities->sum('quantity'),
            'chips' => $this->getChips(),
            'daily_deals' => $this->when('optDailyDeals', $this->optDailyDeals)
        ];

        return array_merge($payload, $this->getPrice());
    }

    private function getPrice(): array
    {
        /* @var WholesalePrice $prices */
        $prices = $this->wholesale_prices->first();
        $originalPrice = optional($prices)->price;
        $price = $originalPrice;
        if ($this->optDailyDeals && $this->optDailyDeals->discount > 0) {
            $price = $originalPrice * (1 - $this->optDailyDeals->discount / 100);
        }
        return [
            'currencySign' => optional($prices)->currency->unicode_symbol,
            'original_price' => $originalPrice,
            'price' => $price,
            'has_stock' => $price !== $originalPrice,
            'price_formatted' => $prices->formatted_price
            //'isPriceSet' => !empty($prices)
        ];
    }
}
