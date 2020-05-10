<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductBatch extends Model
{
    protected $guarded = [];

    public function scopeOfStore($query, $store) {
        $query->where('store_id', $store);
    }

    public function scopeOfProduct($query, $product) {
        $query->where('product_id', $product);
    }
}
