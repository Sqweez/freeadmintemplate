<?php

namespace App\Http\Resources\shop;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\AttributeResource;
use App\Http\Resources\shop\ProductChildResource;


class OrderProductResource extends JsonResource
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
            'product_id' => $this->product->product_id,
            'product_price' => $this->product->product_price,
            'product_name' => $this->product->product_name,
            'product_image' => url('/') . Storage::url($this->product->general_images->first()->image ?? 'products/product_image_default.jpg'),
            'attributes' => $this->product->attributes->merge($this->product->product->attributes)->pluck('attribute_value')
        ];
    }
}
