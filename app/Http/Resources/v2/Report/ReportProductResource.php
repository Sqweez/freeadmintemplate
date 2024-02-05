<?php

namespace App\Http\Resources\v2\Report;

use App\SaleProduct;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/* @mixin SaleProduct */
class ReportProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @mixin SaleProduct
     * @return array
     */
    public function toArray($request = null): array
    {
        return [
            'product_id' => $this->product_id,
            'product_name' => $this->product->product->product_name,
            'attributes' => $this->product->getFullAttributesValues(),
            '_attributes' => $this->product->getFullAttributes(),
            'manufacturer' => $this->product->manufacturer,
            'product_price' => $this->product_price,
            'discount' => $this->discount
        ];
    }
}
