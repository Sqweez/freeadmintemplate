<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $guarded = [];

    public function client() {
        return $this->belongsTo('App\Client', 'client_id')->withDefault([
            'client_name' => 'Гость'
        ]);
    }

    public function store() {
        return $this->belongsTo('App\Store', 'store_id');
    }

    public function products() {
        return $this->hasMany('App\SaleProduct', 'sale_id');
    }

    public function user() {
        return $this->belongsTo('App\User', 'user_id')->withTrashed();
    }

    public function scopeByDate($q, $date) {
        $q->where('created_at', $date);
    }
}
