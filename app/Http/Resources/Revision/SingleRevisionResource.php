<?php

namespace App\Http\Resources\Revision;

use App\v2\Models\Revision;
use Illuminate\Http\Resources\Json\JsonResource;

/* @mixin Revision */
class SingleRevisionResource extends JsonResource
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
            'store' => $this->store,
            'user' => $this->user,
            'status' => $this->getStatusTextAttribute(),
            'files_count' => $this->files->count(),
            'uploaded_files_count' => $this->files->where('uploaded_file_path', '!=', null)->count(),
            'created_at' => format_datetime($this->created_at),
            'link' => sprintf("/revision/%s", $this->id),
            'files' => RevisionFileResource::collection($this->files)
        ];
    }
}
