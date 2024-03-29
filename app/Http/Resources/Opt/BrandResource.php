<?php

namespace App\Http\Resources\Opt;

use App\Manufacturer;
use Illuminate\Http\Resources\Json\JsonResource;

/* @mixin Manufacturer */
class BrandResource extends JsonResource
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
            'name' => $this->manufacturer_name,
            'is_new' => false,
            'link' => $this->getOptLink(),
            'image' => $this->getFullPathImageAttribute(),
            'description' => $this->manufacturer_description,
        ];
    }
}
