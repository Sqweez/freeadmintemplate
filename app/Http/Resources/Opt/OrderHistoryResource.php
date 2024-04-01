<?php

namespace App\Http\Resources\Opt;

use App\v2\Models\WholesaleOrder;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

/* @mixin WholesaleOrder */
class OrderHistoryResource extends JsonResource
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
            'created_at' => Carbon::parse($this->created_at)->format('d.m.Y H:i:s'),
            'expected_arrival_date' => $this->getFormattedExpectedArrivalDateAttribute(),
            'comment' => $this->comment,
            'total_price' => $this->getTotalPriceAttribute(),
            'position_count' => $this->getPositionCountAttribute(),
            'products_count' => $this->products->count(),
            'delivery_price' => $this->delivery_price,
            'status' => $this->currentStatus()->status,
        ];
    }
}
