<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;


/**
 * Class User
 *
 * @mixin \App\Store
 * */
class StoreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'city' => $this->city_name->name,
            'city_id' => $this->city_id,
            'name' => $this->name,
            'type' => $this->type,
            'address' => $this->address ?? "",
            'description' => $this->description ?? "",
            'type_id' => $this->type_id,
            'balance' => $this->balance,
            'iron_balance' => $this->ironBalance,
            'kaspi_terminal_ip' => $this->kaspi_terminal_ip,
            'has_kaspi_terminal' => !!strlen($this->kaspi_terminal_ip),
            'etc' => $this->etc,
        ];
    }
}
