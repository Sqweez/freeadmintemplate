<?php

namespace App\Http\Resources\Fit;

use App\Models\FitUser;
use Illuminate\Http\Resources\Json\JsonResource;

/* @mixin FitUser */
class FitAuthUserResource extends JsonResource
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
            'gym' => $this->gym,
            'gym_id' => $this->gym_id,
            'role' => $this->role,
            'role_id' => $this->fit_role_id,
        ];
    }
}
