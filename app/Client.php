<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{

    use SoftDeletes;

    protected $guarded = [];

    public function transactions() {
        return $this->hasMany('App\ClientTransaction', 'client_id');
    }

    public function sales() {
        return $this->hasMany('App\ClientSale', 'client_id');
    }

    public function orders() {
        return $this->hasMany('App\Order', 'client_id');
    }

    public function city() {
        return $this->belongsTo('App\Store', 'client_city')->withDefault([
            'city' => 'Город не указан'
        ]);
    }

    public function partner_sales() {
        return $this->hasMany('App\Sale', 'partner_id');
    }

    public function getBalanceAttribute() {
        return intval($this->transactions()->sum('amount'));
    }

    public function purchases() {
        return $this->hasMany('App\Sale', 'client_id');
    }

    public function scopeOfPhone($q, $phone) {
        $q->where('client_phone', $phone);
    }

    public function scopeOfToken($q, $token) {
        $q->where('user_token', $token);
    }

    public function scopePartner($query) {
        $query->where('is_partner', true);
    }
}
