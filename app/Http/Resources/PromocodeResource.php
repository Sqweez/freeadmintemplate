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
            'active_until' => format_date($this->active_until),
            'promocode_type' => $this->promocode_type,
            'promocode_type_id' => $this->promocode_type_id,
            'min_total' => intval($this->min_total),
            'required_products' => $this->required_products,
            'free_product_id' => $this->free_product_id,
            'brand_id' => $this->brand_id
        ];
    }
}
