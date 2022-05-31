<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

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

        $subcategories = $request->has('site') ? $this->subcategories->where('is_site_visible', true) : $this->subcategories;

        return [
            'id' => $this->id,
            'name' => $this->category_name,
            'subcategories' => $subcategories,
            'category_img' => $this->category_img,
            'category_slug' => $this->category_slug,
            'seo_text' => $this->seoText
        ];
    }
}
