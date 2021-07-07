<?php

namespace App\Http\Resources\Preorder;

use App\Http\Resources\v2\Report\ReportProductResource;
use App\Sale;
use App\v2\Models\Preorder;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class PreodersListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        //return parent::toArray($request);
        return [
            'id' => $this->id,
            'client' => $this->client,
            'client_id' => $this->client_id,
            'user_id' => $this->user_id,
            'user' => $this->user->name,
            'products' => collect(ReportProductResource::collection($this->products)->toArray($request))->groupBy(['product_id', 'discount'])->map(function ($product, $product_id) {
                return collect($product)->map(function ($p, $discount) {
                    return array_merge(['count' => count($p)], $p[0]);
                });
            })->flatten(1),
            'comment' => $this->comment,
            'store' => $this->store->name,
            'store_id' => $this->store_id,
            'status_text' => Preorder::PREORDER_STATUS[$this->status]['text'],
            'amount' => $this->amount,
            'status' => $this->status,
            'payment_type_text' => Sale::PAYMENT_TYPES[$this->payment_type]['name'],
            'payment_type' => $this->payment_type,
            'date' => Carbon::parse($this->created_at)->format('d.m.Y')
        ];
    }
}
