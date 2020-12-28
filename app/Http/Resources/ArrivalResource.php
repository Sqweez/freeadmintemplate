<?php

namespace App\Http\Resources;

use App\ArrivalProducts;
use App\Product;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use function foo\func;

class ArrivalResource extends JsonResource
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
            'store' => $this->store->name,
            'store_id' => $this->store_id,
            'user_id' => $this->user_id,
            'user' => $this->user->name,
            'is_completed' => $this->is_completed,
            'products' => ArrivalProductResource::collection($this->products),
            'position_count' => $this->products->count(),
            'product_count' => $this->products->sum('count'),
            'total_cost' => $this->products->reduce(function ($a, $c) {
                return $a + intval($c->purchase_price) * intval($c->count);
            }, 0),
            'total_sale_cost' => $this->products->reduce(function ($a, $c) {
                return $a + intval($c->count) * intval($c->product->product_price ?? 0);
            }, 0),
            'date' => Carbon::parse($this->created_at)->format('d.m.Y H:i:s'),
        ];
    }
}
