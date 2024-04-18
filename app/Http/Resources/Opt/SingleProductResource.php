<?php

namespace App\Http\Resources\Opt;

use App\v2\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/* @mixin Product */
class SingleProductResource extends JsonResource
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
        $originalPrice = $this->wholesale_prices->first()->price;
        $price = $originalPrice;
        if ($this->optDailyDeals && $this->optDailyDeals->discount > 0) {
            $price = $originalPrice * (1 - $this->optDailyDeals->discount / 100);
        }
        return [
            'id' => $this->id,
            'product_name' => $this->product_name,
            'category' => $this->category->only(['id', 'category_name']),
            'manufacturer' => $this->manufacturer->only(['id', 'manufacturer_name']),
            'description' => $this->product_description,
            'attributes' => $this->attributes->pluck('attribute_value')->join(','),
            'price' => $price,
            'product_images' => $this->product_images->count() > 0 ? $this->product_images->pluck('image')->map(function ($image) {
                return url('/') . Storage::url($image);
            })->toArray() : [url('/') . Storage::url('products/product_image_default.jpg')],
            'quantity_type' => $this->getWholesaleQuantityType($quantity),
            'chips' => $this->getChips(),
            'daily_deals' => $this->when('optDailyDeals', $this->optDailyDeals),
            'has_stock' => $price !== $originalPrice,
            'original_price' => $originalPrice
        ];
    }
}
