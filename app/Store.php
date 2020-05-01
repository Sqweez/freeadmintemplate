<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $guarded = [];

    public function type() {
        return $this->belongsTo('App\StoreType', 'type_id');
    }
}
