<?php

namespace App\Http\Resources\Opt;

use App\v2\Models\ProductSku;
use App\v2\Models\WholesaleClient;
use Illuminate\Http\Resources\Json\JsonResource;

/* @mixin ProductSku */
class VariantResource extends JsonResource
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
            'name' => $this->attributes->pluck('attribute_value')->join(',') ?: 'Стандартный',
            'quantity' => $this->batches->sum('quantity') - $this->inCartCount(),
        ];
    }

    private function inCartCount()
    {
        /* @var WholesaleClient $user */
        $user = auth()->user();
        if (!$user) {
            return 0;
        }
        return $user->cart->items()->where('product_id', $this->id)->sum('count');
    }
}
