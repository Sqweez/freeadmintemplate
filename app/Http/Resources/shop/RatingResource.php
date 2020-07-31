<?php

namespace App\Http\Resources\shop;

use App\RatingCriteria;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class RatingResource extends JsonResource {
    public function toArray($request) {

        $criterias = collect(RatingCriteria::all());
        $rating = collect($this->rating)->groupBy('criteria_id');

        return ['id' => $this->id, 'name' => $this->name, 'description' => $this->description, 'image' => url('/') . Storage::url($this->image), 'rating' => $criterias->map(function ($item) use ($rating) {
            return ['criteria_name' => $item['criteria'], 'criteria_id' => $item['id'], 'avg_rating' => isset($rating[$item['id']]) ? round($rating[$item['id']]->avg('rating'), 2) : 0];
        }),];
    }
}
