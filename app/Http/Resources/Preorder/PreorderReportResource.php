<?php

namespace App\Http\Resources\Preorder;

use App\Http\Resources\v2\Report\ReportProductResource;
use App\Sale;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class PreorderReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $user_id = $request->get('user_id', null) === null;
        return [
            'id' => $this->id,
            'client' => $this->client->only(['id', 'client_name']),
            'date' => Carbon::parse($this->created_at)->format('d.m.Y H:i:s'),
            'user' => $this->user->only(['id', 'name']),
            'store' => $this->store->only(['id', 'name']),
            'payment_type_text' => Sale::PAYMENT_TYPES[$this->payment_type]['name'] . ' | Предоплата',
            'payment_type' => $this->payment_type,
            'discount' => 0,
            'balance' => 0,
            'products' => collect(ReportProductResource::collection($this->products)->toArray($request))->groupBy(['product_id', 'discount'])->map(function ($product, $product_id) {
                return collect($product)->map(function ($p, $discount) {
                    return array_merge(['count' => count($p)], $p[0]);
                });
            })->flatten(1),
            'store_type' => intval($this->store->type_id),
            'purchase_price' => 0,
            'fact_price' => 0,
            'final_price' => $this->amount,
            'margin' => 0,
            'certificate' => null,
            'split_payment' => null,
            'comment' => $this->comment,
            'sale_type' => 'Предоплата'
        ];
    }
}
