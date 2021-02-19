<?php

namespace App\Http\Resources\shop\v2;

use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
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
            'title' => $this->title,
            'text' => $this->text,
            'image' => url('/') . \Storage::url($this->news_image[0]['image']),
            'short_text' => $this->short_text
        ];
    }
}
