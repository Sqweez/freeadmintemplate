<?php

namespace App\Http\Resources\Stocks;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class StockResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'title' => $this->title,
            'discount' => $this->discount,
            'started_at' => Carbon::parse($this->started_at)->format('d.m.Y H:i:s'),
            'finished_at' => Carbon::parse($this->finished_at)->format('d.m.Y H:i:s'),
            'products' => StockProductResource::collection($this->products),
            'is_active' => $this->started_at <= now() && $this->finished_at >= now()
        ];
    }
}
