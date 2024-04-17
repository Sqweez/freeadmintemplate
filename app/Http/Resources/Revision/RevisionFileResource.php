<?php

namespace App\Http\Resources\Revision;

use App\v2\Models\RevisionFile;
use Illuminate\Http\Resources\Json\JsonResource;

/* @mixin RevisionFile */
class RevisionFileResource extends JsonResource
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
            'category' => $this->category,
            'original_file' => $this->getOriginalFileFullPathAttribute(),
            'uploaded_file' => $this->getUploadedFileFullPathAttribute(),
            'uploaded_at' => format_datetime($this->uploaded_at)
        ];
    }
}
