<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $guarded = [];

    public function client() {
        return $this->belongsTo('App\Client', 'client_id');
    }

    public function store() {
        return $this->belongsTo('App\Store', 'store_id');
    }

    public function products() {
        return $this->hasMany('App\SaleProduct', 'sale_id');
    }
}
