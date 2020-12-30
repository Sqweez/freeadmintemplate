<?php

namespace App\Http\Resources\shop\v2;

use App\v2\Models\ProductSku;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductSkuResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @mixin ProductSku
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'attribute' => $this->attributes->pluck('attribute_value')->first() ?? 'Неизвестно',
            'quantity' => $this->batches->where('store_id', $request->get('store_id', 1))->sum('quantity')
        ];
    }
}
