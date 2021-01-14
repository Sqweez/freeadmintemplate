<?php

namespace App\Http\Resources;

use App\Http\Resources\shop\ProductsResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\v2\Models\Product;
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
            'category_id' => $this->category_id,
            'subcategory_id' => $this->subcategory_id,
            'name' => $this->name,
            'category' => $this->category->category_name,
            'subcategory' => $this->subcategory->subcategory_name,
            'products' => ProductsResource::collection(Product::with(['subcategory', 'attributes', 'product_thumbs'])->whereIn('id', $this->products)->get()),
            'description' => $this->description,
        ];
    }
}
