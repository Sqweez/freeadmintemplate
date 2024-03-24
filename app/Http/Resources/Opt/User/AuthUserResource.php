<?php

namespace App\Http\Resources\Opt\User;

use App\v2\Models\WholesaleClient;
use Illuminate\Http\Resources\Json\JsonResource;

/* @mixin WholesaleClient */
class AuthUserResource extends JsonResource
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
            'access_token' => $this->access_token,
            'cart_length' => 10,
            'cart_total' => 35000
        ];
    }
}
