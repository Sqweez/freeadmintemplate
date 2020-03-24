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

        static::deleting(function($category) { // before delete() method call this
            $category->subcategories()->delete();
            // do the rest of the cleanup...
        });
    }

}
