<?php

namespace App\Http\Resources;

use App\Promocode;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/* @mixin Promocode */
class PromocodeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
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
            'min_total' => $this->min_total,
            'required_products' => $this->required_products,
            'free_product_id' => $this->free_product_id,
            'brand_id' => $this->brand_id,
            'promocode_apply_type_id' => $this->promocode_apply_type_id,
            'promocode_apply_type' => $this->promocode_apply_type,
            'title' => $this->title,
            'total_use_quantity' => $this->total_use_quantity,
            'per_client_use_quantity' => $this->per_client_use_quantity,
            'apply_to_clients_id' => $this->apply_to_clients_id,
            'available_stores' => $this->available_stores
        ];
    }
}
