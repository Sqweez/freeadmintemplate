<?php

namespace App\Http\Resources;

use App\Manufacturer;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/* @mixin Manufacturer */

class ManufacturerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $is_shop = $request->has('shop');

        return [
            'id' => $this->id,
            'manufacturer_name' => $this->manufacturer_name,
            'manufacturer_img' => $this->manufacturer_img ? url('/') . Storage::url($this->manufacturer_img) : null,
            'manufacturer_description' => $this->manufacturer_description,
            'show_on_main' => $this->show_on_main
        ];
    }
}
