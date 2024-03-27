<?php

namespace App\Http\Resources\Opt;

use App\Subcategory;
use Illuminate\Http\Resources\Json\JsonResource;

/* @mixin Subcategory */
class SubcategoryResource extends JsonResource
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
            'name' => $this->subcategory_name,
            'is_new' => false,
        ];
    }
}
