<?php

namespace App\Http\Resources\Matrix;

use App\v2\Models\Matrix;
use Illuminate\Http\Resources\Json\JsonResource;

/* @mixin Matrix */
class MatrixListResource extends JsonResource
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
            'store' => $this->store,
            'position_count' => count($this->products),
            'products' => $this->products,
        ];
    }
}
