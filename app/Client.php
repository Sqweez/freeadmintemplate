<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $guarded = [];

    public function transactions() {
        return $this->hasMany('App\ClientTransaction', 'client_id');
    }

    public function sales() {
        return $this->hasMany('App\ClientSale', 'client_id');
    }

    public function scopeOfPhone($q, $phone) {
        $q->where('client_phone', $phone);
    }

    public function scopeOfToken($q, $token) {
        $q->where('user_token', $token);
    }
}
