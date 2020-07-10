<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoryProduct extends Model
{
    protected $table = 'category_product';

    protected $guarded = [];

    public $timestamps = false;

    public function product() {
        return $this->hasOne('App\Product', 'id', 'product_id');
    }

    public function category() {
        return $this->belongsTo('App\Category');
    }
}
