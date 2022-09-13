<?php

namespace App\Http\Resources;

use App\ArrivalProducts;
use App\Product;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Arrival;

/**
 * Class Arrival
 *
 * @mixin Arrival
 * */
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

        $products = collect(ArrivalProductResource::collection($this->products))->map(function ($product) {
            return $product;
        });

        return [
            'id' => $this->id,
            'store' => $this->store->name,
            'store_id' => $this->store_id,
            'user_id' => $this->user_id,
            'user' => $this->user->name,
            'is_completed' => $this->is_completed,
            'products' => $products,
            'position_count' => $this->products->count(),
            'product_count' => $this->products->sum('count'),
            'total_cost' => $this->products->reduce(function ($a, $c) {
                return $a + intval($c->purchase_price) * intval($c->count);
            }, 0),
            'total_sale_cost' => $this->products->reduce(function ($a, $c) {
                return $a + intval($c->count) * intval($c->product->product_price ?? 0);
            }, 0),
            'date' => format_datetime($this->created_at),
            'arrive_date' => format_date($this->arrived_at),
            'arrived_at' => $this->arrived_at,
            'comment' => $this->comment,
            'bookings' => $this->bookings,
            'payment_cost' => $this->payment_cost,
            'search' => trim($products->reduce(function ($a, $c) {
                return $a . sprintf(
                        " %s %s %s ",
                        $c['product_name'],
                        $c['manufacturer']['manufacturer_name'],
                        collect($c['attributes'])->pluck('attribute_value')->join(' ')
                    );
            }, '')),
        ];
    }
}
