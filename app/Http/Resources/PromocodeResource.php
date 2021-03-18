<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PromocodeResource extends JsonResource
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
            'discount' => intval($this->discount),
            'client_id' => $this->client_id,
            'partner' => $this->partner,
            'promocode' => $this->promocode,
            'is_active' => !!$this->is_active
        ];
    }
}
