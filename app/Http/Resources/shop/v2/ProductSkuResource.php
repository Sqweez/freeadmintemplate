<?php

namespace App\Http\Resources\shop\v2;

use App\v2\Models\Product;
use App\v2\Models\ProductSku;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProductSkuResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @mixin ProductSku
     * @return array
     */
    public function toArray($request)
    {
        $store_id = intval($request->get('store_id'));
        $batches = $store_id === -1 ? $this->batches->whereIn('store_id', [1, 6]) : $this->batches->where('store_id', $store_id);
        $quantity = $this->getQuantity($batches, intval($request->get('store_id')));
        return [
            'id' => $this->id,
            'attribute' => $this->attributes->pluck('attribute_value')->first() ?? 'Неизвестно',
            'quantity' => $quantity['quantity'],
            'store_id' => $quantity['store_id'],
            'product_sku_image' => $this->product_images->count() ? url('/') . Storage::url($this->product_images->first()->image) : null,
        ];
    }

    private function getQuantity($batches, $store_id) {
        $initial_quantity = $batches->where('store_id', $store_id)->sum('quantity');
        if ($initial_quantity === 0) {
            $other_quantities = $batches->where('quantity', '>', 0)->groupBy('store_id');
            if (count($other_quantities) === 0) {
                return [
                    'store_id' => $store_id,
                    'quantity' => 0
                ];
            }
            else {
                $quantity = collect($other_quantities)->map(function ($q, $key) {
                    return [
                        'store_id' => intval($key),
                        'quantity' => collect($q)->reduce(function ($a, $c) {
                            return $a + intval($c['quantity']);
                        }, 0)
                    ];
                })->values()->sortBy('quantity')->reduce(function ($a, $c) {
                    return $a + intval($c['quantity']);
                });
                return [
                    'store_id' => -2,
                    'quantity' => $quantity,
                ];
            }
        }
        return [
            'store_id' => $store_id,
            'quantity' => $initial_quantity
        ];
    }
}
