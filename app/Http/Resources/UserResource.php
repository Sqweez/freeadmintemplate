<?php

namespace App\Http\Resources;

use App\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

/* @mixin User */
class UserResource extends JsonResource
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
            'name' => $this->name,
            'store_id' => $this->store_id,
            'city' => $this->role_id == 1 ? 'Все города' : $this->store->city,
            'login' => $this->login,
            'role_id' => $this->role_id,
            'role' => $this->role->role_name,
            'store_slug' => Str::slug($this->store->city_name->name),
            // 'token' => $this->token,
            'store' => $this->store,
            'has_access' => $this->hasWorkingAccess(),
            'should_open_shift' => $this->getShouldOpenShift()
        ];
    }
}
