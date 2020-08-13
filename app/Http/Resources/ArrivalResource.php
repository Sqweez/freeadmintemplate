<?php

namespace App\Http\Resources;

use App\Product;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ArrivalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $products = ProductRevisionResource::collection(Product::find($this->products->pluck('product_id')));
        $_products = $this->products;

        $products = collect($products)->map(function ($i, $key) use ($_products) {
            $i['count'] = $_products[$key]['count'];
            $i['purchase_price'] = $_products[$key]['purchase_price'];
            unset($i['quantity']);
            return $i;
        });

        return [
            'id' => $this->id,
            'store' => $this->store->name,
            'store_id' => $this->store_id,
            'user_id' => $this->user_id,
            'user' => $this->user->name,
            'is_completed' => !!$this->is_completed,
            'products' => $products,
            'position_count' => collect($_products)->sum('count'),
            'product_count' => $_products->count(),
            'date' => Carbon::parse($this->created_at)->format('d.m.Y'),
        ];
    }
}
