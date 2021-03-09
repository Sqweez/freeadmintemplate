<?php

namespace App\Http\Resources\v2\Order;

use App\Order;
use Carbon\Carbon;
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
        $_items = collect($this->items);
        $items = collect($this->items)->unique('product_id')->map(function ($item) use ($_items) {
            $item['count'] = $_items->filter(function ($_item) use ($item) {
                return $_item['product_id'] === $item['product_id'];
            })->count();
            return $item;
        })->values();
        return [
            'id' => $this->id,
            'client_name' => $this->fullname,
            'is_authorized' => !($this->client_id === -1),
            'store' => $this->store,
            'email' => $this->email,
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
            'city_text' => $this->city_text->name ?? 'Неизвестно',
            'comment' => $this->comment,
            'total_price' => ceil($this->items->reduce(function($a, $c) {
                return $a + intval($c['product_price']);
            }, 0)  - $this->items->reduce(function($a, $c) {
                    return $a + intval($c['product_price']);
                }, 0) * ($this->discount / 100)),
            'products' => collect(OrderProductsResource::collection($items)),
            '_products' => collect(OrderProductsResource::collection($this->items)),
            'date' => Carbon::parse($this->created_at)->format('d.m.Y H:i:s'),
            'is_paid_text' => $this->is_paid ? 'Оплачен' : 'Не оплачен',
            'is_paid' => $this->is_paid,
            'image' => count($this->image) ? $this->image[0]['image'] : null,
        ];
    }
}
