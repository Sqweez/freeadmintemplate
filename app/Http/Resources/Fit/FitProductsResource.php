<?php

namespace App\Http\Resources\Fit;

use App\v2\Models\ProductSku;
use Illuminate\Http\Resources\Json\JsonResource;


/* @mixin ProductSku */
class FitProductsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'name' => $this->getName(),
            'quantity' => $this->batches->sum('quantity'),
            'category' => $this->category,
            'manufacturer' => $this->manufacturer,
            'barcode' => $this->product_barcode,
            'price' => $this->product->product_price,
        ];
    }

    private function getName(): string {
        $output = [];
        $output[] = $this->product->product_name;
        $output[] = trim($this->product->attributes->pluck('attribute_value')->join(' '));
        $output[] = trim($this->attributes->pluck('attribute_value')->join(' '));
        return collect($output)
            ->filter(function ($value) {
                return $value;
            })
            ->values()
            ->join(', ');
    }
}
