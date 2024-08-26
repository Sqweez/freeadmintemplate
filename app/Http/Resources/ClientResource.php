<?php

namespace App\Http\Resources;

use App\Client;
use Carbon\Carbon;
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
    public function toArray($request): array {

        /*$last_sale = $this->sales->sortByDesc('created_at')->first();
        $last_sale_date = null;
        if ($last_sale) {
            $last_sale_date = Carbon::parse($last_sale['created_at'])->format('d.m.Y H:i:s');
        }*/

        return [
            'id' => $this->id,
            'client_name' => $this->client_name,
            'client_phone' => $this->client_phone,
            'client_card' => $this->client_card,
            'client_discount' => max($this->calculateDiscountPercent(), $this->loyalty->discount),
            'client_initial_discount' => $this->calculateDiscountPercent(),
            'client_balance' => $this->cached_balance,
            'total_sum' => $this->cached_total_sale_amount,
            'current_month_sum' => 0,//$this->current_month_sales_amount,
            'until_platinum' => 0,//max(0, Client::PLATINUM_CLIENT_MONTHLY_THRESHOLD - $this->current_month_sales_amount),
            'until_platinum_percent' => 0,//max(0, (100 - (100 * $this->current_month_sales_amount / Client::PLATINUM_CLIENT_MONTHLY_THRESHOLD))),
            'is_partner' => !!$this->is_partner,
            'city' => $this->city->name,
            'client_city' => $this->client_city,
            'loyalty' => $this->loyalty,
            'loyalty_id' => $this->loyalty->id,
            'job' => $this->job,
            'instagram' => $this->instagram,
            'photo' => $this->photo,
            'last_sale_date' => null,//$last_sale_date,
            'gender_name' => Client::GENDERS[$this->gender],
            'gender' => $this->gender,
            'birth_date_formatted' => $this->birth_date ? Carbon::parse($this->birth_date)->format('d.m.Y') : 'Не указана',
            'birth_date' => $this->birth_date,
            'is_wholesale_buyer' => $this->is_wholesale_buyer,
            'amount' => $this->cached_total_sale_amount,
            'wholesale_type' => $this->wholesale_type,
            'wholesale_type_id' => $this->wholesale_type_id,
            'wholesale_status' => $this->wholesale_status,
            'wholesale_status_text' => $this->wholesale_status_text,
            'is_kaspi' => $this->is_kaspi,
            'barter_balance_amount' => $this->barter_balance->reduce(function ($a, $c) {
                return $a + $c->amount;
            }, 0),
            'test' => false,
            //'last_mailing_date' => $this->lastMailing ? format_datetime($this->lastMailing->created_at) : 'Никогда'
        ];
    }
}
