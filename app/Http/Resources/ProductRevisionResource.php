<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed category
 * @property mixed product_name
 * @property mixed id
 * @property mixed attributes
 * @property mixed manufacturer
 * @property mixed product_price
 */
class ProductRevisionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $quantity =
            $request->has('store_id') ?
                $this->quantity->where('store_id', $request->get('store_id'))->sum('quantity') :
                $this->quantity->where('store_id', 1)->sum('quantity');

        return [
            'id' => intval($this->id),
            'product_name' => $this->product_name,
            'categories' => $this->category->category_name,
            'attributes' => AttributeResource::collection($this->attributes),
            'manufacturer' => $this->manufacturer->manufacturer_name,
            'product_price' => $this->product_price,
            'quantity' => $quantity
        ];
    }
}
