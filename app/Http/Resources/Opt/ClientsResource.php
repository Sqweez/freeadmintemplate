<?php

namespace App\Http\Resources\Opt;

use App\v2\Models\WholesaleClient;
use Illuminate\Http\Resources\Json\JsonResource;

/* @mixin WholesaleClient */
class ClientsResource extends JsonResource
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
            'name' => $this->getFullNameAttribute(),
            'city' => $this->city,
            'phone' => $this->phone,
            'email' => $this->email,
        ];
    }
}
