<?php

namespace App\Http\Resources\Fit;

use App\Models\FitClient;
use Illuminate\Http\Resources\Json\JsonResource;

/* @mixin  FitClient */
class FitSingleClientResource extends JsonResource
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
            'pass' => $this->pass,
            'date' => format_datetime($this->created_at),
            'phone' => $this->phone,
            'balance' => $this->balance,
            'birth_date' => $this->birth_date,
            'birth_date_format' => format_date($this->birth_date),
            'services' => FitPurchasedServiceResource::collection($this->purchased_services)
        ];
    }
}
