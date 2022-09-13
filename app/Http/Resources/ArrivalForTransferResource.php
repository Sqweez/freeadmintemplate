<?php

namespace App\Http\Resources;

use App\Arrival;
use Illuminate\Http\Resources\Json\JsonResource;

/* @mixin Arrival */

class ArrivalForTransferResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array {

        $totalCost = $this->products->reduce(function ($a, $c) {
            return $a + optional($c->product)->product_price * $c['count'];
        }, 0);

        return [
            'id' => $this->id,
            'comment' => $this->comment,
            'full_name' => sprintf(
                "%s | %s тенге | %sшт. | %s",
                    format_datetime($this->created_at),
                    $totalCost,
                    $this->product_count,
                    $this->comment,
            ),
            'product_ids' => $this->products->pluck('product_id')
        ];
    }
}
