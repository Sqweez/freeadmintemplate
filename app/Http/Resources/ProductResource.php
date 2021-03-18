<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request) {

        $quantity =
            $request->has('store_id') ?
                $this->quantity->where('store_id', $request->get('store_id'))->sum('quantity') :
                $this->quantity;

        $price = $request->has('store_id') ? $this->price->where('store_id', $request->get('store_id')) : $this->price;

        return [
            'id' => intval($this->id),
            'product_name' => $this->product_name,
            'product_description' => $this->product_description,
            'categories' => $this->categories,
            'subcategories' => $this->subcategories,
            'attributes' => AttributeResource::collection($this->attributes),
            'manufacturer' => $this->manufacturer->first()->manufacturer_name ?? '',
            'manufacturer_id' => $this->manufacturer->first()->id ?? '',
            'product_price' => $this->product_price,
            'prices' => $this->price,
            'quantity' => $quantity,
            'product_barcode' => $this->product_barcode,
            'group_id' => $this->group_id,
            'product_images' => $this->product_images->pluck('product_image'),
            'is_hit' => !!$this->is_hit,
            'tags' => $this->tag,
        ];
    }
}
