<?php

namespace App\Http\Resources;

use App\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

/**
* @mixin Category
 */

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array {
        return [
            'id' => $this->id,
            'name' => $this->category_name,
            'subcategories' => $this->subcategories,
            'category_img' => $this->category_img,
            'category_slug' => $this->category_slug,
            'seo_text' => $this->seoText,
            'is_site_visible' => !!$this->is_site_visible,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_h1' => $this->meta_h1
        ];
    }
}
