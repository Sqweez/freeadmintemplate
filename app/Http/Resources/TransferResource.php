<?php

namespace App\Http\Resources;

use App\Product;
use App\ProductBatch;
use Illuminate\Http\Resources\Json\JsonResource;

class TransferResource extends JsonResource
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
            'parent_store' => $this->parent_store->name,
            'child_store' => $this->child_store->name,
            'user' => 'Администратор',
            'product_count' => $this->batches->count(),
            'position_count' => $this->batches->unique('product_id')->count(),
            'total_cost' => $this->batches->reduce(function ($a, $c) {
                return $a + intval($c->product_price);
            }, 0),
            'total_purchase_cost' => $this->batches->reduce(function ($a, $c) {
                return $a + intval($c->purchase_price);
            }, 0),
            'photos' => json_decode($this->photos, true)
        ];
    }
}
