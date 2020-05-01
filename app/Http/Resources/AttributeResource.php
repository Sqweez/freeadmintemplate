<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AttributeResource extends JsonResource
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
            'attribute' => $this->attribute_name->attribute_name,
            'attribute_id' => $this->attribute_id,
            'attribute_value' => $this->attribute_value,
        ];
    }
}
