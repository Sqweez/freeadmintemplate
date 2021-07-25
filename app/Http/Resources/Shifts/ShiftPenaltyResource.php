<?php

namespace App\Http\Resources\Shifts;

use Illuminate\Http\Resources\Json\JsonResource;
use App\v2\Models\ShiftPenalty;

/* @mixin ShiftPenalty */

class ShiftPenaltyResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => $this->user,
            'author' => $this->author,
            'amount' => $this->amount,
            'comment' => $this->comment,
            'date' => $this->date,
        ];
    }
}
