<?php

namespace App\Http\Resources\v2\Order;

use App\Order;
use Illuminate\Http\Resources\Json\JsonResource;

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
        return [
            'id' => $this->id,
            'client_name' => $this->fullname,
            'is_authorized' => !($this->client_id === -1),
            'store' => $this->store,
            'status' => $this->status,
            'status_text' => Order::ORDER_STATUS[$this->status]['text'],
            'payment' => $this->payment,
            'payment_text' => Order::ORDER_PAYMENT[$this->payment]['text'],
            'delivery' => $this->delivery,
            'delivery_text' => Order::ORDER_DELIVERY[$this->delivery]['text'],
            'discount' => $this->discount,
            'balance' => $this->balance,
            'address' => $this->address,
            'phone' => $this->phone,
            'city' => $this->city,
            'comment' => $this->comment,
            'total_price' => $this->items->reduce(function($a, $c) {
                return $a + intval($c['product_price']);
            }, 0),
            'products' => OrderProductsResource::collection($this->items)
        ];
    }
}
