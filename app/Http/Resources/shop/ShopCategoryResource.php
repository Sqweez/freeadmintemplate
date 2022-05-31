<?php

namespace App\Http\Resources\shop;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ShopCategoryResource extends JsonResource
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
            'subcategories' => $this->subcategories->where('is_site_visible', true)->toArray(),
            'category_img' => url('/') . Storage::url($this->category_img),
            'category_slug' => $this->category_slug,
            'seo_text' => $this->seoText
        ];
    }
}
