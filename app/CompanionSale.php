<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanionSale extends Model
{
    protected $guarded = [
        'id'
    ];

    protected $casts = [
        'id' => 'integer',
        'discount' => 'integer'
    ];

    public function products() {
        return $this->hasMany('App\v2\CompanionSaleProduct', 'companion_sale_id');
    }

    public function user() {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function store() {
        return $this->belongsTo('App\Store', 'store_id');
    }

    public function companion() {
        return $this->belongsTo('App\Store', 'store_id');
    }
}
