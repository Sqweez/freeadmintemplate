<?php

namespace App\Http\Resources\shop;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\shop\OrderProductResource;
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
        $productCollection = collect(OrderProductResource::collection(Product::find($products->pluck('product_id'))));
        $productCollection = $productCollection->map(function ($i) use ($products) {
            $i['count'] = $products->filter(function ($c) use ($i){
                return $c['product_id'] == $i['product_id'];
            })->count();
            return $i;
        });

        return [
            'status' => $this->status ?? 1,
            'id' => $this->id,
            'products' => $productCollection,
            'created_at' => $this->created_at,
        ];
    }
}
