<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SingleTransferResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        //$products = BatchResource::collection($this->batches);
        //$products = $products->toArray($request);

        return [
            'id' => $this->id,
            'parent_store' => $this->parent_store_id,
            'child_store' => $this->child_store_id,
            'products' => BatchResource::collection($this->batches->groupBy('product_id')->map(function ($batch) {
                return $batch->map(function ($product) use ($batch) {
                    $product['count'] = $batch->count();
                    return $product;
                });
            })->flatten()->unique('product_id')->values())
        ];
    }

}
