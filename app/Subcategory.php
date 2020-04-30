<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    protected $guarded = [];

    public function scopeOfSlug($query, $slug) {
        return $query->where('subcategory_slug', $slug);
    }
}
