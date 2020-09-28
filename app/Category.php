<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use function foo\func;

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
            Cache::forget('categories');
            Cache::forget('shop-categories');
        });

        static::creating(function() {
            Cache::forget('categories');
            Cache::forget('shop-categories');
        });

        static::updating(function() {
            Cache::forget('categories');
            Cache::forget('shop-categories');
        });
    }

    private function clearCache() {
        Cache::forget('categories');
        Cache::forget('shop-categories');
    }

    public function scopeOfSlug($query, $slug) {
        return $query->where('category_slug', $slug);
    }

}
