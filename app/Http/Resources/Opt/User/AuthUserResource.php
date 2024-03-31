<?php

namespace App\Http\Resources\Opt\User;

use App\Repository\Opt\CartRepository;
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
     * @throws \Exception
     */
    public function toArray($request)
    {
        $cartData = (new CartRepository(auth()->user()))->getTotal();

        return [
            'id' => $this->id,
            'name' => $this->getFullNameAttribute(),
            'access_token' => $this->access_token,
        ] + $cartData;
    }
}
