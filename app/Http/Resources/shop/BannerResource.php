<?php

namespace App\Http\Resources\shop;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class BannerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $is_site = $request->has('site');
        return [
            'id' => $this->id,
            'image' => $is_site ? url('/') . Storage::url($this->image) : $this->image,
            'description' => $this->description,
            'order' => intval($this->order),
            'is_active' => !!$this->is_active,
        ];
    }
}
