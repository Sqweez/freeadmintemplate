<?php

namespace App\Http\Resources\v2\Report;

use App\Sale;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class Arrival
 *
 * @mixin \App\Sale
 * */

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
        $user_id = $request->get('user_id', null) === null;
        $user = auth()->user();
        $canBeChanged = !is_null($user) && ((!$user->isStoreKeeper() || $this->payment_type === __hardcoded(4)));
        return [
            'id' => $this->id,
            'client' => $this->client,
            'date' => $this->date,
            'user' => $this->user,
            'store' => $this->store,
            'payment_type_text' => Sale::PAYMENT_TYPES[$this->payment_type]['name'],
            'payment_type' => $this->payment_type,
            'discount' => $this->discount,
            'balance' => $this->balance,
            'products' => collect(ReportProductResource::collection($this->products)->toArray($request))->groupBy(['product_id', 'discount'])->map(function ($product, $product_id) {
                return collect($product)->map(function ($p, $discount) {
                    return array_merge(['count' => count($p)], $p[0]);
                });
            })->flatten(1)->merge([$this->certificate->id ? collect([
                'product_name' => $this->certificate->used ? 'Сертификат на сумму '.  $this->certificate->amount .' тенге (Использован)' : 'Сертификат на сумму '.  $this->certificate->amount .' тенге',
                'count' => 1,
                'discount' => 0,
                'attributes' => [],
                'manufacturer' => [
                    'manufacturer_name' => ''
                ],
                'certificate_id' => $this->certificate->id,
                'product_price' => $this->certificate->amount
            ]) : []])->filter(function ($q) {return count($q) > 0;}),
            'store_type' => intval($this->store->type_id),
            'purchase_price' => $user_id ? $this->purchase_price : 0,
            'fact_price' => $this->product_price + $this->certificate->final_amount,
            'final_price' => $this->final_price - $this->preorder->amount,
            'margin' => $this->margin,
            'certificate' => $this->used_certificate,
            'split_payment' => $this->split_payment !== null ?
                collect($this->split_payment)->map(function ($split) {
                    $split['payment_text'] = Sale::PAYMENT_TYPES[intval($split['payment_type'])]['name'];
                    $split['payment_type'] = intval($split['payment_type']);
                    return $split;
                })
                : null,
            'comment' => $this->comment,
            'preorder' => $this->preorder,
            'is_delivery' => $this->is_delivery,
            'is_booking' => (bool)$this->booking,
            'booking_paid_sum' => $this->booking ? $this->booking->paid_sum : 0,
            'is_paid' => $this->is_paid,
            'kaspi_red_commission' => $this->kaspi_red_commission,
            'is_kaspi_red' => $this->kaspi_red,
            'is_confirmed' => $this->is_confirmed,
            'is_full_wholesale_purchase' => $this->is_opt,
            'promocode' => $this->promocode,
            'paid_by_barter_balance' => $this->paid_by_barter_balance,
            'can_be_changed' => $canBeChanged
        ];
    }
}
