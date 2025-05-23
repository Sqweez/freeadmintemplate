<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class User
 *
 * @mixin \App\Transfer
 * */
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
        /*try {
            $search = $this->batches
                ->groupBy('product_id')
                ->map(function($batch) {
                    return $batch[0];
                })
                ->values()
                ->reduce(function($a, $c) {
                    $product = $c['product']['product'];
                    return $a . ' '
                        . $product['product_name']
                        . ' ' . $product['manufacturer']['manufacturer_name'] . ' '
                        . collect($product['attributes'])->map(function ($attribute) {
                            return $attribute['attribute_value'];
                        })->join(' ') . ' ' .
                        collect($c['product']['attribute'])->map(function ($attribute) {
                            return $attribute['attribute_value'];
                        })->join(' ');
                }, '');
        } catch (\Exception $exception) {
            $search = '';
        }*/

        return [
            'id' => $this->id,
            'parent_store' => optional($this->parent_store)->name ?? 'Удаленный магазин',
            'child_store' => optional($this->child_store)->name ?? 'Удаленный магазин',
            'child_store_id' => $this->child_store->id,
            'parent_store_id' => $this->parent_store_id,
            'user' => $this->whenLoaded('user') ? $this->user->name : 'Администратор',
            'product_count' => $this->batches->count(),
            'position_count' => $this->batches->unique('product_id')->count(),
            'total_cost' => $this->batches->reduce(function ($a, $c) {
                $cost = intval($c->product->product->product_price ?? 0);
                return $a + ($cost - ($cost * $c->discount / 100));
            }, 0),
            'total_purchase_cost' => $this->batches->reduce(function ($a, $c) {
                $cost = $c->productBatch->purchase_price ?? 0;
                return $a + ($cost - ($cost * $c->discount / 100));
            }, 0),
            'photos' => json_decode($this->photos, true),
            'date' => Carbon::parse($this->created_at)->format('d.m.Y'),
            'date_updated' => $this->updated_at ? Carbon::parse($this->updated_at)->format('d.m.Y') : null,
            'is_consignment' => $this->companionSale->is_consignment,
            'search' => '',
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'is_accepted' => $this->is_accepted,
            'is_confirmed' => $this->is_confirmed
        ];
    }
}
