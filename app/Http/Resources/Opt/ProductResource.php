<?php

namespace App\Http\Resources\Opt;

use App\Http\Resources\Opt\Product\PriceResource;
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
    public function toArray($request): array
    {
        $quantity = $this->batches->sum('quantity');

        return [
            'id' => $this->id,
            'product_name' => trim(
                sprintf("%s %s", $this->product_name, $this->attributes->pluck('attribute_value')->join(','))
            ),
            'subcategory' => $this->subcategory,
            'product_image' => $this->retrieveProductThumb(),
            'slug' => $this->getOptLink(),
            'quantity_type' => $this->getWholesaleQuantityType($quantity),
            'chips' => $this->getChips(),
            'daily_deals' => $this->when('optDailyDeals', $this->optDailyDeals),
            'prices' => PriceResource::collection($this->wholesale_prices)->map(function ($resource) {
                    return new PriceResource($resource, $this->retrieveActiveDiscountPercent());
                }),
            'discount' => $this->retrieveActiveDiscountPercent(),
            'has_stock' => $this->retrieveActiveDiscountPercent() !== 0,
        ];
    }
}
