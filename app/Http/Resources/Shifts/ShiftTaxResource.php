<?php

namespace App\Http\Resources\Shifts;

use Illuminate\Http\Resources\Json\JsonResource;

class ShiftTaxResource extends JsonResource
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
            'store' => $this->store,
            'store_id' => $this->store_id,
            'shift_tax' => $this->shift_tax,
            'sale_percent' => $this->sale_percent,
        ];
    }
}
