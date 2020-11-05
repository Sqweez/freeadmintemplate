<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Promocode extends Model
{
    protected $fillable = ['client_id', 'promocode', 'discount', 'is_active', 'id'];

    public function partner() {
        return $this->belongsTo('App\Client', 'client_id');
    }
}
