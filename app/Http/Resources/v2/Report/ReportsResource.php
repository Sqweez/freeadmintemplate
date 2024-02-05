<?php

namespace App\Http\Resources\v2\Report;

use App\Sale;
use App\SaleProduct;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/**
 * Class Arrival
 *
 * @mixin Sale
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
            'fit_client' => $this->fit_client,
            'date' => $this->date,
            'user' => $this->user,
            'store' => $this->store,
            'payment_type_text' => Sale::PAYMENT_TYPES[$this->payment_type]['name'],
            'payment_type' => $this->payment_type,
            'discount' => $this->discount,
            'balance' => $this->balance,
            'products' => $this->getProducts($this->products),
            'store_type' => $this->store->type_id,
            'purchase_price' => $user_id ? $this->purchase_price : 0,
            'fact_price' => $this->getRealPrice(),
            'final_price' => $this->final_price,
            'margin' => $this->margin,
            'certificate' => $this->used_certificate,
            'split_payment' => $this->split_payment !== null ? collect($this->split_payment)->map(function ($split) {
                $split['payment_text'] = Sale::PAYMENT_TYPES[intval($split['payment_type'])]['name'];
                $split['payment_type'] = intval($split['payment_type']);
                return $split;
            }) : null,
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


    /**
     * @param  Collection|SaleProduct[]  $products
     *
     */
    protected function getProducts($products): Collection
    {
        return collect(ReportProductResource::collection($products)->toArray(request()))
            ->groupBy(['product_id', 'discount'])
            ->flatMap(function ($product) {
                return collect($product)->map(function ($p) {
                    return array_merge(['count' => count($p)], $p[0]);
                });
            })
            ->when($this->certificate && $this->certificate->id, function ($collection) {
                return $collection->push($this->getCertificateDetails());
            })
            ->filter(function ($q) {
                return count($q) > 0;
            });
        $transformedProducts = collect(ReportProductResource::collection($products)->toArray(request()));
        $groupedProducts = $transformedProducts->groupBy(['product_id', 'discount']);
        return $groupedProducts->map(function (Collection $productsGroup) {
            $product = $productsGroup->first();
            return $product->toArray() + ['count' => $productsGroup->count()];
        })->values()->when($this->certificate && $this->certificate->id, function (Collection $collection) {
            return $collection->push($this->getCertificateDetails());
        });
    }

    protected function getCertificateDetails(): array
    {
        return [
            'product_name' => $this->certificate->getCertificateName(),
            'count' => 1,
            'discount' => 0,
            'attributes' => [],
            'manufacturer' => [
                'manufacturer_name' => ''
            ],
            'certificate_id' => $this->certificate->id,
            'product_price' => $this->certificate->amount
        ];
    }
}
