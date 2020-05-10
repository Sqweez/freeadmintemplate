<?php

namespace App\Http\Resources;

use App\Product;
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
            'fullname' => $this->fullname,
            'status' => $this->status,
            'email' => $this->email,
            'comment' => $this->comment,
            'phone' => $this->phone,
            'address' => $this->address,
            '_products' => ProductResource::collection(Product::find($this->items->pluck('product_id')))
        ];
    }
}
