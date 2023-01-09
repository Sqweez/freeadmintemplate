<?php

namespace App\Http\Resources;

use App\Promocode;
use Illuminate\Http\Resources\Json\JsonResource;

/* @mixin Promocode */
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
            'discount' => $this->discount,
            'client_id' => $this->client_id,
            'partner' => $this->partner,
            'promocode' => $this->promocode,
            'is_active' => !!$this->is_active,
            'active_until' => format_date($this->active_until)
        ];
    }
}
