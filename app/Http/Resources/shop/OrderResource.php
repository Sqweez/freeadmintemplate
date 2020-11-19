<?php

namespace App\Http\Resources\shop;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\shop\OrderProductResource;
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

    protected $statuses = [
        [
            'id' => 0,
            'text' => 'В обработке'
        ],
        [
            'id' => 1,
            'text' => 'Выполнен'
        ],
        [
            'id' => -1,
            'text' => 'Отменен'
        ]
    ];

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
            'date' => Carbon::parse($this->created_at)->format('H:i:s d.m.Y'),
            'status_text' => $this->getOrderStatusText($this->status ?? 1)
        ];
    }

    private function getOrderStatusText($status) {
        return collect($this->statuses)->filter(function ($i) use ($status){
            return $i['id'] === $status;
        })->first()['text'];
    }
}
