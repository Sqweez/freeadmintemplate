<?php

namespace App\Http\Resources\Fit;

use Illuminate\Http\Resources\Json\JsonResource;

class FitClientsListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
