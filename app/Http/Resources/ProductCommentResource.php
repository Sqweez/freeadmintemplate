<?php

namespace App\Http\Resources;

use App\v2\Models\ProductComment;
use Illuminate\Http\Resources\Json\JsonResource;

/* @mixin ProductComment */
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
            'name' => $this->getCommentName(),
            'date' => $this->date,
            'parent_id' => $this->parent_id,
            'is_employee' => !!$this->user_id
        ];
    }
}
