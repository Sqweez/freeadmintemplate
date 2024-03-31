<?php

namespace App\Http\Resources\Opt;

use App\v2\Models\ProductSku;
use Illuminate\Http\Resources\Json\JsonResource;

/* @mixin ProductSku */
class VariantResource extends JsonResource
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
            'name' => $this->attributes->pluck('attribute_value')->join(',') ?? 'Стандартный',
            'quantity' => $this->batches->sum('quantity'),
        ];
    }
}
