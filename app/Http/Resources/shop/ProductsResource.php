<?php

namespace App\Http\Resources\shop;

use App\Http\Resources\AttributeResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\shop\ProductRangeResource;

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
                'product_id' => $this->id,
                'product_name' => $this->product_name,
                'category' => $this->categories[0]->category_name ?? '',
                'category_id' => $this->categories[0]->id ?? 0,
                'subcategory' => $this->subcategories[0]->subcategory_name ?? '',
                'subcategory_id' => $this->subcategories[0]->id ?? 0,
                'manufacturer_id' => $this->manufacturer[0]->id ?? 0,
                'product_price' => $this->product_price,
                'product_image' => url('/') . Storage::url($this->product_thumbs[0]->product_image ?? 'product_thumbs/product_image_default.webp'),
                'in_stock' =>collect(ProductRangeResource::collection($this->children))->sum('quantity') > 0,
                'attributes' => AttributeResource::collection($this->attributes),
        ];
    }

    private function inStock($quantity) {
        return array_reduce($quantity->toArray($quantity), function ($a, $c) {
            return $a + $c['quantity'];
        } , 0) > 0;
    }
}
