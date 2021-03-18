<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed product_price
 * @property mixed manufacturer
 * @property mixed product_name
 * @property mixed subcategory
 * @property mixed category
 * @property mixed id
 */
class MainProductsResource extends JsonResource
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
            'categories' => $this->category->id,
            'subcategories' => $this->subcategory->id,
            'product_name' => $this->product_name . ' ' .($this->manufacturer->manufacturer_name) . ' | ' . $this->product_price . ' тнг'
        ];
    }
}
