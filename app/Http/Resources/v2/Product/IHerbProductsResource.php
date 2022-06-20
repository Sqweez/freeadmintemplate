<?php

namespace App\Http\Resources\v2\Product;

use App\v2\Models\ProductSku;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 *
 * @mixin ProductSku
 * */


class IHerbProductsResource extends JsonResource
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
            'product_id' => $this->product_id,
            'category' => $this->category,
            'manufacturer' => $this->manufacturer,
            'product_name' => $this->product_name,
            'excel_name' => $this->excel_name,
            'attributes' => $this->mergeAttributes($this->attributes, $this->product->attributes),
            'total_quantity' => $this->batches->sum('quantity'),
            'purchase_price' => $this->batches->last()->purchase_price ?? 0,
            'product_price' => $this->product_price,
            'final_price' => 0
        ];
    }
}
