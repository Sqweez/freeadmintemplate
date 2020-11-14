<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{

    use SoftDeletes;

    protected $guarded = [];

    public function type() {
        return $this->belongsTo('App\StoreType', 'type_id');
    }
}
