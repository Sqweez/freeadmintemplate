<?php

namespace App\Http\Resources\Opt\Admin;

use App\v2\Models\WholesaleOrder;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

/* @mixin WholesaleOrder */
class OrdersResource extends JsonResource
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
            'is_paid' => $this->is_paid,
            'created_at' => Carbon::parse($this->created_at)->format('d.m.Y H:i:s'),
            'expected_arrival_date' => $this->getFormattedExpectedArrivalDateAttribute(),
            'comment' => $this->comment,
            'currency_id' => $this->currency_id,
            'total_price' => $this->getTotalPriceAttribute(),
            'position_count' => $this->getPositionCountAttribute(),
            'products_count' => $this->products->count(),
            'delivery_price' => $this->delivery_price,
            'status' => $this->status->status->description,
            'status_id' => $this->status->wholesale_status_id,
            'waybill' => $this->waybill ? (url('/') . \Storage::url($this->waybill)) : null,
            'invoice' => $this->invoice ? (url('/') . \Storage::url($this->invoice)) : null,
            'client' => [
                'id' => $this->wholesale_client_id,
                'name' => $this->client->getFullNameAttribute(),
            ],
            'contacts' => [
                [
                    'label' => 'Почта',
                    'value' => $this->email,
                ],
                [
                    'label' => 'Телефон',
                    'value' => $this->phone,
                ],
                [
                    'label' => 'Контактное лицо',
                    'value' => $this->name,
                ],
                [
                    'label' => 'Адрес',
                    'value' => $this->client->delivery_address,
                ],
                [
                    'label' => 'Город',
                    'value' => $this->client->city->name,
                ],
            ],
            'is_editing_disabled' => $this->getIsEditingDisabledAttribute()
        ];
    }
}
