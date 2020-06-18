<?php

namespace App\Http\Resources\shop;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\shop\ProductsResource;
use App\Product;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $products = $this->items ?? $this->products;

        return [
            'status' => $this->status ?? 1,
            'products' => ProductsResource::collection(Product::find($products->pluck('product_id'))),
        ];
    }
}
