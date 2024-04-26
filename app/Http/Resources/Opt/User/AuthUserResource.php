<?php

namespace App\Http\Resources\Opt\User;

use App\v2\Models\WholesaleClient;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/* @mixin WholesaleClient */
class AuthUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     * @throws Exception
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->getFullNameAttribute(),
            'access_token' => $this->access_token,
            'phone' => $this->phone,
            'has_russian_passport' => $this->has_russian_passport,
            'patronymic' => $this->patronymic,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'legal_type_id' => $this->legal_type_id,
            'iin' => $this->iin,
            'passport' => $this->passport,
            'delivery_address' => $this->delivery_address,
            'city' => $this->city->only(['id', 'name']),
            'email' => $this->email,
            'preferred_currency_id' => $this->preferred_currency_id,
        ];
    }
}
