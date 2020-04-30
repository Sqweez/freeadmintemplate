<?php

namespace App\Http\Resources\shop;

use App\Http\Resources\AttributeResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

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

        $store_id = $request->cookie('store_id') ?? 1;

        return [
            'product_id' => $this->product_id,
            'category_id' => $this->product->categories[0]->id,
            'subcategory_id' => $this->product->subcategories[0]->id,
            'product_name' => $this->product->product_name,
            'subcategory' => $this->product->subcategories[0]->subcategory_name,
            'product_price' => $this->product->product_price,
            'product_image' => url('/') . Storage::url($this->product->product_images[0]->product_image ?? 'products/product_image_default.jpg'),
            'in_stock' => $this->inStock($this->product->quantity->where('store_id', $store_id)),
            'attributes' => AttributeResource::collection($this->product->attributes),
        ];
    }

    private function inStock($quantity) {
        return array_reduce($quantity->toArray($quantity), function ($a, $c) {
            return $a + $c['quantity'];
        } , 0) > 0;
    }
}
