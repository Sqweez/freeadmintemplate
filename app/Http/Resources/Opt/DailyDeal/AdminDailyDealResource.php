<?php

namespace App\Http\Resources\Opt\DailyDeal;

use App\v2\Models\OptDailyDeal;
use Illuminate\Http\Resources\Json\JsonResource;


/* @mixin OptDailyDeal */
class AdminDailyDealResource extends JsonResource
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
            'from_date' => format_datetime($this->active_from),
            'to_date' => format_datetime($this->active_to),
            'product_count' => $this->items_count,
            'is_active' => $this->is_active,
            'remaining_time' => $this->remaining_time,
        ];
    }
}
