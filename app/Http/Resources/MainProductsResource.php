<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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
            'categories' => $this->categories->pluck('id'),
            'subcategories' => $this->subcategories->pluck('id'),
            'product_name' => $this->product_name . ' ' .($this->manufacturer[0]->manufacturer_name ?? '') . ' | ' . $this->product_price . ' тнг'
        ];
    }
}
