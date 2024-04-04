<?php

namespace App\Http\Resources\shop;

use App\CartProduct;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

/* @mixin CartProduct */
class CartProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $quantity = $this->product->batches->/*where('store_id', $request->get('store_id', 1))->*/sum('quantity') ?? 0;
        $count = $this->count;
        $change = false;
        if ($quantity < $count) {
            $count = $quantity;
            $change = true;
            CartProduct::where('cart_id', $this->cart_id)->where('product_id', $this->product_id)->update(['count' => $count]);
        }

        return [
            'id' => $this->product->id,
            'cart_item_id' => $this->id,
            'count' => $count,
            'quantity' => $quantity,
            'change' => $change,
            'product_name' => $this->product->product_name,
            'product_image' => url('/') . \Storage::url($this->product->general_thumbs->pluck('image')->first() ?? 'products/thumbs/product_image_default_300x300.jpg'),
            'attribute_sku' => $this->product->attributes->pluck('attribute_value'),
            'attribute_product' => $this->product->product->attributes->pluck('attribute_value'),
            'subcategory' => $this->product->subcategory->subcategory_name,
            'is_site_visible' => $this->product->is_site_visible,
            'product_price' => $this->product->product->stock_price,
            'iherb_price' => $this->product->product->iherb_price ?: $this->product->product->product_price,
            'product_name_slug' => Str::slug($this->product->product_name, '-'),
            'product_id' => $this->product->product_id,
            'discount' => $this->discount,
            'final_price' => $this->product->product->stock_price - ($this->product->product->stock_price * $this->discount / 100),
            'product_price_rub' => $this->product->product->product_price_rub,
            'manufacturer_id' => $this->product->product->manufacturer_id,
            'category_id' => $this->product->product->category_id,
            'cart_product_id' => $this->id,
            'variants' => $this->product->relativeSku->map(function ($item) {
                return [
                    'id' => $item['id'],
                    'attribute_value' => $item->attributes->pluck('attribute_value')->join(' ') ?: 'Стандартный',
                    'quantity' => $item->batches->sum('quantity'),
                ];
            })
        ];
    }
}
