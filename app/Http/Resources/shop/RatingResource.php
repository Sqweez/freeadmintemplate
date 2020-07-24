<?php

namespace App\Http\Resources\shop;

use App\RatingCriteria;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class RatingResource extends JsonResource
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
            'description' => $this->description,
            'image' => url('/') . Storage::url($this->image),
            'rating' => collect($this->rating)->groupBy('criteria_id')->map(function ($item, $key) {
                return [[
                    'avg_rating' => $item->avg('rating'),
                    'criteria_name' => RatingCriteria::find($key)->criteria,
                ]];
            })->flatten(1),
        ];
    }
}
