<?php

namespace App\Http\Resources;

use App\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class BatchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $product = new ProductResource(Product::find($this->productBatch->product_id));
        $product = $product->toArray($request);

        return [
            'batch_id' => $this->batch_id,
            'product_id' => $this->productBatch->product_id,
            'product_name' => $product['product_name'],
            'attributes' => $product['attributes'],
        ];
    }
}
