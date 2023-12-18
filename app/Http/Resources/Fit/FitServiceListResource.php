<?php

namespace App\Http\Resources\Fit;

use App\Models\FitService;
use Illuminate\Http\Resources\Json\JsonResource;

/* @mixin FitService */
class FitServiceListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'visits_count_text' => $this->getVisitsCountTextAttribute(),
            'visits_count' => $this->visits_count,
            'validity_in_days_text' => $this->getValidityInDaysTextAttribute(),
            'validity_in_days' => $this->validity_in_days,
        ];
    }
}
