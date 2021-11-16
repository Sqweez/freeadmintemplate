<?php

namespace App\Http\Resources\shop;

use App\Http\Resources\shop\OrderProductResource;
use App\Order;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use App\Product;
use Carbon\Carbon;

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

        $productCollection = OrderProductResource::collection($this->items ?? $this->products)
            ->groupBy('product_id')
            ->map(function ($product) {
                return array_merge($product[0]->toArray(request()), ['count' => count($product)]);
            })->values();
        return [
            'status' => $this->status ?? 1,
            'id' => $this->id,
            'created_at' => $this->created_at,
            'products' => $productCollection,
            'date' => Carbon::parse($this->created_at)->format('H:i:s d.m.Y'),
            'status_text' => Order::ORDER_STATUS[$this->status ?? 1]['text'],
            'image' => count($this->image) ? url('/') . Storage::url($this->image[0]['image']) : null
        ];
    }

}
