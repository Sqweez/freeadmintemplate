<?php

namespace App\Http\Resources\shop;

use App\Banner;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/* @mixin Banner */
class BannerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request\
     * @mixin Banner
     * @return array
     */
    public function toArray($request)
    {
        $is_site = $request->has('site');
        return [
            'id' => $this->id,
            'image' => $is_site ? url('/') . Storage::url($this->image) : $this->image,
            'mobile_image' => $is_site ? url('/') . Storage::url($this->mobile_image) : $this->mobile_image,
            'description' => $this->description,
            'order' => $this->order,
            'is_active' => $this->is_active,
            'website' => $this->website,
            'cities' => $this->cities,
            'url' => $this->when($this->description && \Str::length($this->description) > 1, function () {
                return "/cart?stock=" . \Str::slug($this->description);
            })
        ];
    }
}
