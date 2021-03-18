<?php

namespace App\Http\Resources;

use App\Http\Resources\shop\ProductsResource;
use App\Http\Resources\shop\v2\ProductSkuResource;
use Illuminate\Http\Resources\Json\JsonResource;

class RelatedProductsResource extends JsonResource
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
            'category_name' => $this->category_name,
            'related_products' => $this->relatedProducts//ProductsResource::collection,
        ];
    }
}
