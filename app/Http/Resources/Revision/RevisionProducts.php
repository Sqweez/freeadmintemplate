<?php

namespace App\Http\Resources\Revision;

use App\v2\Models\RevisionProduct;
use Illuminate\Http\Resources\Json\JsonResource;

/* @mixin RevisionProduct */
class RevisionProducts extends JsonResource
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
            'count_actual' => $this->count_actual,
            'count_expected' => $this->count_expected,
            'price' => price_format($this->product->product_price),
            'delta' => $this->difference_count,
            'name' => sprintf(
                "%s %s %s %s",
                $this->product->manufacturer->manufacturer_name,
                $this->product->product_name,
                $this->product->attributes->pluck('attribute_value')->join(' '),
                $this->sku->attributes->pluck('attribute_value')->join(' '),
            ),
        ];
    }
}
