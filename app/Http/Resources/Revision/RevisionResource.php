<?php

namespace App\Http\Resources\Revision;

use App\v2\Models\Revision;
use Illuminate\Http\Resources\Json\JsonResource;

/* @mixin Revision */
class RevisionResource extends JsonResource
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
            'user' => $this->user,
            'status' => $this->getStatusTextAttribute(),
            'store' => $this->store,
            'files_count' => $this->files_count,
            'uploaded_files_count' => $this->files_uploaded_count,
            'created_at' => format_datetime($this->created_at),
            'link' => sprintf("/revision/%s", $this->id),
            'percentage_completed' => $this->percentage_completed
        ];
    }
}
