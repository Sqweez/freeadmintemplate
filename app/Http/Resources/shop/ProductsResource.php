<?php

namespace App\Http\Resources\shop;

use App\v2\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/* @mixin Product */

class ProductsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $store_id = intval($request->get('store_id'));
        $batches = $store_id === -1 ? $this->batches->whereIn('store_id', [1, 6]) : $this->batches->where('store_id', $store_id);
        $user_token = $request->get('user_token');

        $in_selected_city = collect($batches)->filter(function ($batch) use ($store_id) {
                return $batch['store_id'] == $store_id;
            })->count() > 0;
        $in_other_city = collect($batches)->filter(function ($batch) use ($store_id) {
                return $batch['store_id'] != $store_id;
            })->count() > 0;

        return [
            'product_id' => intval($this->id),
            'is_hit' => !!$this->is_hit,
            'product_name' => strlen($this->product_name_web) ? $this->product_name_web : $this->product_name,
            'subcategory' => $this->subcategory->subcategory_name,
            'subcategory_id' => $this->subcategory->id,
            'product_price' => $this->product_price,
            'product_image' => url('/') . Storage::url($this->product_thumbs[0]->image ?? 'products/product_image_default.jpg'),
            'attributes' => $this->attributes->pluck('attribute_value'),
            'product_name_slug' => Str::slug($this->product_name, '-'),
            'in_selected_city' => $in_selected_city,
            'in_other_city' => $in_other_city,
            'is_favorite' => !!$this->favorite,
            'category_id' => intval($this->category_id),
            'stock_price' => $this->stock_price,
            'has_stock' => $this->stock_price !== $this->product_price
        ];
    }
}
