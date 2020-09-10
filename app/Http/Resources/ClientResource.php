<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $total = $this->sales->sum('amount');

        $sale_discount = $this->getSaleDiscount($total);

        return [
            'id' => $this->id,
            'client_name' => $this->client_name,
            'client_phone' => $this->client_phone,
            'client_card' => $this->client_card,
            'client_discount' => min(max($this->client_discount, $sale_discount), 100),
            'client_balance' => $this->transactions->sum('amount'),
            'total_sum' => $total,
            'is_partner' => !!$this->is_partner,
            'city' => $this->city->city,
            'client_city' => $this->client_city
        ];
    }

    private function getSaleDiscount($total) {
        if ($total > 30000) {
            return 10;
        }
        if ($total > 15000) {
            return 5;
        }

        return 0;
    }
}
