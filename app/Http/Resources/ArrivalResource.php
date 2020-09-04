<?php

namespace App\Http\Resources;

use App\Product;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use function foo\func;

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
        $_products = collect($this->products);

        $products = collect($products)->map(function ($i, $key) use ($_products) {
            $product = $_products->first(function ($item) use ($i) {
                return $item->product_id == $i['id'];
            });
            $i['count'] = intval($product['count']);
            $i['purchase_price'] = $product['purchase_price'];
            $i['sort_id'] = intval($product['id']);
            unset($i['quantity']);
            return $i;
        })->sortBy('sort_id')->values();

        return [
            'id' => $this->id,
            'store' => $this->store->name,
            'store_id' => $this->store_id,
            'user_id' => $this->user_id,
            'user' => $this->user->name,
            'is_completed' => !!$this->is_completed,
            'products' => $products,
            'product_count' => collect($_products)->sum('count'),
            'position_count' => $_products->count(),
            'total_cost' => collect($_products)->reduce(function($a, $c) {
                return ($c['purchase_price'] * $c['count']) + $a;
            }, 0) . "₸",
            'date' => Carbon::parse($this->created_at)->format('d.m.Y'),
        ];
    }
}
