<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class RevisionResource extends JsonResource
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
            'user' => $this->user,
            'store' => $this->store,
            'store_id' => $this->store_id,
            'user_id' => $this->user_id,
            'created_at' => Carbon::parse($this->created_at)->format('d.m.Y')
        ];
    }
}
