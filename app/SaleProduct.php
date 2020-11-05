<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SaleProduct extends Model
{
    protected $guarded = [];
    public $timestamps = false;

    public function products() {
        return $this->belongsTo('App\Product', 'product_id');
    }

    public function sale() {
        return $this->belongsTo('App\Sale', 'sale_id');
    }
}
