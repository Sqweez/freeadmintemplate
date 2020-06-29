<?php

namespace App\Http\Resources;

use App\Http\Resources\shop\ProductsResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Product;
use Illuminate\Support\Facades\Storage;

class GoalPartResource extends JsonResource
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
            'category_id' => intval($this->category_id),
            'subcategory_id' => intval($this->subcategory_id),
            'name' => $this->name,
            'category' => $this->category,
            'subcategory' => $this->subcategory,
            'products' => ProductsResource::collection(Product::find($this->products->pluck('product_id'))),
            'description' => $this->description,
            'product_ids' => $this->products->pluck('product_id')->map(function ($i) {
                return intval($i);
            })
        ];
    }
}
