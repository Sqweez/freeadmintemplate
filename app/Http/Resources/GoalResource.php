<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class GoalResource extends JsonResource
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
            'name' => $this->name,
            'parts' => GoalPartResource::collection($this->parts),
            'image' => $this->image ? url('/') . Storage::url($this->image) : null,
            'mobile_image' => $this->mobile_image ? url('/') . Storage::url($this->mobile_image) : null,
            'image_origin' => $this->image,
            'mobile_image_origin' => $this->mobile_image
        ];
    }
}
