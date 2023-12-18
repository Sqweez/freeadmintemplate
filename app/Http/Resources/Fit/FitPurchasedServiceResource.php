<?php

namespace App\Http\Resources\Fit;

use App\Models\FitServiceSale;
use Illuminate\Http\Resources\Json\JsonResource;

/* @mixin FitServiceSale */
class FitPurchasedServiceResource extends JsonResource
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
            'purchase_date' => format_datetime($this->created_at),
            'activation_date' => $this->activated_at ? format_datetime($this->activated_at) : 'Не активирован',
            'service' => $this->service,
            'user' => $this->user->only(['id', 'name']),
            'is_activated' => $this->is_activated,
            'valid_until' => $this->getValidUntilAttributeText(),
            'visits_remaining' => $this->getVisitsRemainingAttributeText(),
            'can_be_used' => $this->getCanBeUsed(),
            'client_id' => $this->client_id,
        ];
    }
}
