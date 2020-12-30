<?php

namespace App\Http\Resources\shop;

use App\Http\Resources\shop\v2\ProductSkuResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;


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
            'product_id' => $this->id,
            'product_price' => $this->product_price,
            'subcategory' => $this->subcategory->subcategory_name,
            'product_name' => $this->product_name,
            'product_description' => $this->product_description,
            'attributes' => $this->attributes->pluck('attribute_value'),
            'product_images' => $this->product_images->pluck('image')->map(function ($image) {
                return url('/') . Storage::url($image);
            }),
            'is_hit' => $this->is_hit,
            'is_site_visible' => $this->is_site_visible,
            'skus' => collect(ProductSkuResource::collection($this->sku))->filter(function ($i) {
                return $i['quantity'] > 0;
            })->toArray(),
        ];
    }
}
