<?php

namespace App\Http\Resources\Opt;

use App\Category;
use Illuminate\Http\Resources\Json\JsonResource;

/* @mixin Category */

class CategoryResource extends JsonResource
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
            'name' => $this->category_name,
            'is_new' => false,
            'subcategories' => SubcategoryResource::collection($this->subcategories),
        ];
    }
}
