<?php

namespace App\Http\Resources\shop;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductsResource extends JsonResource
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
            'product_id' => intval($this->id),
            'is_hit' => !!$this->is_hit,
            'product_name' => $this->product_name,
            'subcategory' => $this->subcategory->subcategory_name,
            'subcategory_id' => $this->subcategory->id,
            'product_price' => $this->product_price,
            'product_image' => url('/') . Storage::url($this->product_thumbs[0]->image ?? 'products/product_image_default.jpg'),
            'attributes' => $this->attributes->pluck('attribute_value'),
            'product_name_slug' => Str::slug($this->product_name, '-'),
        ];
    }
}
