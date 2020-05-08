<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $guarded = [];

    public function products() {
        return $this->hasMany('App\CartProduct', 'cart_id');
    }

    public function scopeOfUser($q, $token) {
        return $q->where('user_token', $token);
    }

}
