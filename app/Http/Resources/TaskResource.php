<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'title' => $this->title,
            'text' => $this->text,
            'store_id' => $this->store_id,
            'author' => $this->author->name,
            'store' => $this->store->name,
            'start_date' => Carbon::parse($this->date_start)->format('d.m.Y'),
            'finish_date' => Carbon::parse($this->date_finish)->format('d.m.Y'),
            'date_start' => $this->date_start,
            'date_finish' => $this->date_finish,
            'attachments' => $this->attachments->map(function($a) {
                $a['link'] = url('/') . '/storage/' . $a['url'];
                return $a;
            }),
            'is_completed' => $this->is_completed,
            'is_completion_required' => $this->is_completion_required
        ];
    }
}
