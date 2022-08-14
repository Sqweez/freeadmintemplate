<?php

namespace App\Http\Resources\WithDrawal;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class WithDrawalListResource extends JsonResource
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
            'amount' => $this->amount,
            'user' => $this->user,
            'store' => $this->store,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d'),
            'date' => Carbon::parse($this->created_at)->format('d.m.Y H:i:s'),
            'description' => $this->description,
            'image' => \Storage::url($this->image),
            'can_delete' => !!$this->can_delete
        ];
    }
}
