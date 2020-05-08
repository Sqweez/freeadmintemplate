<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CartProduct extends Model
{
    protected $guarded = [];

    public function product() {
        return $this->belongsTo('App\Product', 'product_id');
    }

    public function scopeCart($query, $cart_id) {
        $query->where('cart_id', $cart_id);
    }

    public function scopeProduct($query, $product_id) {
        $query->where('product_id', $product_id);
    }

    public function scopeStore($query, $store_id) {
        //
    }

}
