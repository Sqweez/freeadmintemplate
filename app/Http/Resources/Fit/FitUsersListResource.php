<?php

namespace App\Http\Resources\Fit;

use App\Models\FitUser;
use Illuminate\Http\Resources\Json\JsonResource;

/* @mixin FitUser */
class FitUsersListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'login' => $this->login,
            'role' => $this->role,
            'password' => ''
        ];
    }
}
