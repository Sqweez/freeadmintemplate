<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
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
            'city' => $this->city,
            'name' => $this->name,
            'type' => $this->type,
            'address' => $this->address ?? "",
            'description' => $this->description ?? "",
            'type_id' => $this->type_id,
        ];
    }
}
