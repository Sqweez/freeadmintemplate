<?php

namespace App\Http\Resources\Revision;

use App\v2\Models\Revision;
use Illuminate\Http\Resources\Json\JsonResource;

/* @mixin Revision */
class RevisionTable extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'products' => RevisionProducts::collection($this->products),
            'count_expected' => $this->products->sum('count_expected'),
            'count_actual' => $this->products->sum('count_actual'),
            'count_delta' => $this->products->sum('count_actual') - $this->products->sum('count_expected'),
            'price_actual_total' => price_format($this->actual_total_price),
            'price_expected_total' => price_format($this->expected_total_price),
            'price_delta' => $this->actual_total_price - $this->expected_total_price,
        ];
    }
}
