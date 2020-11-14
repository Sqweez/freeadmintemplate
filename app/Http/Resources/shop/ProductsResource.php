<?php

namespace App\Http\Resources\shop;

use App\Http\Resources\AttributeResource;
use App\Http\Resources\shop\ProductRangeResource;
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

        $store_id = $request->get('store_id') ?? 1;
        $attributes = $this->attributes;
        $price = $this->price->where('store_id', $store_id)->first()['price'] ?? $this->product_price;

        return [
            'product_id' => intval($this->id),
            'is_hit' => !!$this->is_hit,
            'product_name' => $this->product_name,
            'category' => $this->categories[0]->category_name ?? '',
            'category_id' => $this->categories[0]->id ?? 0,
            'subcategory' => $this->subcategories[0]->subcategory_name ?? '',
            'subcategory_id' => $this->subcategories[0]->id ?? 0,
            'manufacturer_id' => $this->manufacturer[0]->id ?? 0,
            'product_price' => $price,
            'product_image' => url('/') . Storage::url($this->product_images[0]->product_image ?? 'products/product_image_default.jpg'),
            'in_stock' =>collect(ProductRangeResource::collection($this->children))->sum('quantity') > 0,
            'attributes' => AttributeResource::collection($attributes),
            'product_weight' => $this->getProductWeight($attributes) ?? '',
            'product_taste' => $this->getProductWeight($attributes) ?? '',
            'product_name_slug' => Str::slug($this->product_name, '-')
        ];
    }

    private function getProductWeight($attributes) {
        return $attributes->filter(function ($i) {
                return $i['attribute_id'] == 2;
            })->first()['attribute_value'] ?? '';
    }

    private function inStock($quantity) {
        return array_reduce($quantity->toArray($quantity), function ($a, $c) {
                return $a + $c['quantity'];
            } , 0) > 0;
    }

    private function getProductTaste($attributes) {
        return $attributes->filter(function ($i) {
                return $i['attribute_id'] == 1;
            })->first()['attribute_value'] ?? '';
    }
}
