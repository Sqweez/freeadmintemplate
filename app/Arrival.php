<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Arrival extends Model
{
    protected $guarded = [];

    public function products() {
        return $this->hasMany('App\ArrivalProducts');
    }

    public function _products() {
        return $this->belongsToMany('App\Product', 'arrival_products');
    }

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function store() {
        return $this->belongsTo('App\Store');
    }
}
