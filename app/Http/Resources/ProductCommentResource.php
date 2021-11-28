<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductCommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array {
        return [
            'id' => $this->id,
            'comment' => $this->comment,
            'name' => $this->client ? $this->client->client_name : ($this->user ? $this->user->name : 'Гость'),
            'date' => $this->date,
            'parent_id' => $this->parent_id,
            'is_employee' => !!$this->user_id
        ];
    }
}
