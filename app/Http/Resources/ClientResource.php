<?php

namespace App\Http\Resources;

use App\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/* @mixin \App\Client */

class ClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * Class Client
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $total = $this->sales->sum('amount');
        return [
            'id' => $this->id,
            'client_name' => $this->client_name,
            'client_phone' => $this->client_phone,
            'client_card' => $this->client_card,
            'client_discount' => max($this->calculateDiscountPercent(), $this->loyalty->discount),
            'client_initial_discount' => $this->calculateDiscountPercent(),
            'client_balance' => $this->transactions->sum('amount'),
            'total_sum' => $total,
            'is_partner' => !!$this->is_partner,
            'city' => $this->city->name,
            'client_city' => $this->client_city,
            'loyalty' => $this->loyalty,
            'loyalty_id' => $this->loyalty->id
        ];
    }
}
