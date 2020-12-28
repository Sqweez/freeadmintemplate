<?php

namespace App\Http\Resources\v2\Report;

use App\Sale;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @mixin Sale
     * @return array
     */
    public function toArray($request)
    {
      /*  dd($this->products->groupBy(['product_id', 'discount'])->map(function ($product, $product_id) {
            return collect($product)->map(function ($p, $discount) {
                return array_merge(['count' => count($p)], $p[0]->toArray());
            });
        })->flatten(1));*/
        return [
            'id' => $this->id,
            'client' => $this->client->only(['id', 'client_name']),
            'date' => $this->date,
            'user' => $this->user->only(['id', 'name']),
            'store' => $this->store->only(['id', 'name']),
            'payment_type_text' => Sale::PAYMENT_TYPES[$this->payment_type]['name'],
            'payment_type' => $this->payment_type,
            'discount' => $this->discount,
            'balance' => $this->balance,
            'products' => collect(ReportProductResource::collection($this->products)->toArray($request))->groupBy(['product_id', 'discount'])->map(function ($product, $product_id) {
                return collect($product)->map(function ($p, $discount) {
                    return array_merge(['count' => count($p)], $p[0]);
                });
            })->flatten(1),
            'store_type' => intval($this->store->type_id),
            'purchase_price' => $this->purchase_price,
            'fact_price' => $this->product_price,
            'final_price' => $this->final_price,
            'margin' => $this->margin,
        ];
    }
}
