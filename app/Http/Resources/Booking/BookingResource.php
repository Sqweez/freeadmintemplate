<?php

namespace App\Http\Resources\Booking;
use App\v2\Models\Booking;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class Booking
 *
 * @mixin Booking
 * */


class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        //return parent::toArray($request);

        return [
            'id' => $this->id,
            'products' => BookingProductResource::collection($this->products),
            'store' => $this->store,
            'client' => $this->client,
            'user' => $this->user,
            'paid_sum' => $this->paid_sum,
            'date_create' => $this->date_create,
            'status' => $this->status,
            'is_sold' => $this->is_sold,
            'can_sold' => !!$this->arrival->is_completed
        ];
    }
}
