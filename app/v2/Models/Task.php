<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'id' => 'integer',
        'author_id' => 'integer',
        'store_id' => 'integer',
        'user_id' => 'integer',
        'is_completion_required' => 'boolean',
        'is_completed' => 'boolean'
    ];

    public function store() {
        return $this->belongsTo('App\Store', 'store_id');
    }

    public function author() {
        return $this->belongsTo('App\User', 'author_id');
    }

    public function attachments() {
        return $this->hasMany('App\v2\Models\TaskAttachment', 'task_id');
    }

    public function user() {
        return $this->belongsTo('App\User', 'user_id')->withDefault([
            'id' => -1,
            'name' => 'Не установлен'
        ]);
    }
}
