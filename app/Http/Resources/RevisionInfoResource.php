<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RevisionInfoResource extends JsonResource
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
            'product' => new ProductRevisionResource($this->product),
            'stock_quantity' => $this->stock_quantity,
            'fact_quantity' => $this->fact_quantity,
        ];
    }
}
