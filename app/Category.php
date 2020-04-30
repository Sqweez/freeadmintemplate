<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $guarded = [];

    public function subcategories() {
        return $this->hasMany('App\Subcategory', 'category_id');
    }

    public static function boot() {
        parent::boot();

        static::deleting(function($category) {
            $category->subcategories()->delete();
        });
    }

    public function scopeOfSlug($query, $slug) {
        return $query->where('category_slug', $slug);
    }

}
