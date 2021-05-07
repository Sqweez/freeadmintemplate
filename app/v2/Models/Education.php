<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'id' => 'integer',
        'author_id' => 'integer'
    ];

    public function attachments() {
        return $this->hasMany('App\v2\Models\EducationAttachment', 'education_id');
    }

    public function author() {
        return $this->belongsTo('App\User', 'author_id');
    }
}
