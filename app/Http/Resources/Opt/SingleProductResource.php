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
        return [
            'id' => $this->id,
            'product_name' => $this->product_name,
            'category' => $this->category->only(['id', 'category_name']),
            'manufacturer' => $this->manufacturer->only(['id', 'manufacturer_name']),
            'description' => $this->product_description,
            'attributes' => $this->attributes->pluck('attribute_value')->join(','),
            'price' => 1000,
            'product_images' => $this->product_images->count() > 0 ? $this->product_images->pluck('image')->map(function ($image) {
                return url('/') . Storage::url($image);
            })->toArray() : [url('/') . Storage::url('products/product_image_default.jpg')],
        ];
    }
}