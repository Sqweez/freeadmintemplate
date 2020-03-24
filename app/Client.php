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
}
