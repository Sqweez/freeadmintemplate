<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductBatch extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function scopeOfStore($query, $store) {
        $query->where('store_id', $store);
    }

    public function scopeOfProduct($query, $product) {
        $query->where('product_id', $product);
    }
}
