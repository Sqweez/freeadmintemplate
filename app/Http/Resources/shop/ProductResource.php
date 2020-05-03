<?php

namespace App\Http\Resources\shop;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\AttributeResource;
use App\Http\Resources\shop\ProductChildResource;


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
            'subcategory' => $this->subcategories[0]->subcategory_name ?? '',
            'product_name' => $this->product_name,
            'product_description' => $this->product_description,
            'product_images' => $this->getProductImages($this->product_images),
            'children' => count($this->children) > 0 ? ProductRangeResource::collection($this->children) : ProductRangeResource::collection($this->parent->children),
            'product_weight' => $this->getProductWeight($this->attributes),
            'group_id' => $this->group_id,
        ];
    }

    private function getProductImages($images) {
        $images = array_map(function ($i) {
            return url('/') . Storage::url($i->product_image ?? 'products/product_image_default.jpg');
        }, $images->toArray($images));


        if (count($images) === 0) {
            $images[] = url('/') . Storage::url('products/product_image_default.jpg');
        }

        return $images;
    }

    private function getProductWeight($attributes) {
        return $attributes->filter(function ($i) {
            return $i['attribute_id'] === 2;
        })->first()['attribute_value'] ?? '';
    }
}
