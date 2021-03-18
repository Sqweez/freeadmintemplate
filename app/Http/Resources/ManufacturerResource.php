<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

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
            'manufacturer_img' =>
                $is_shop ? ($this->manufacturer_img ? url('/') . Storage::url($this->manufacturer_img) : null) : $this->manufacturer_img,
            'manufacturer_description' => $this->manufacturer_description
        ];
    }
}
