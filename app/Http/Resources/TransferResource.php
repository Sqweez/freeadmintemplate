<?php

namespace App\Http\Resources;

use App\Product;
use App\ProductBatch;
use Carbon\Carbon;
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
                return $a + intval($c->product->product->product_price);
            }, 0),
            'total_purchase_cost' => $this->batches->reduce(function ($a, $c) {
                return $a + intval($c->productBatch->purchase_price ?? 0);
            }, 0),
            'photos' => json_decode($this->photos, true),
            'date' => Carbon::parse($this->created_at)->format('d.m.Y'),
            'date_updated' => Carbon::parse($this->updated_at)->format('d.m.Y')
        ];
    }
}
