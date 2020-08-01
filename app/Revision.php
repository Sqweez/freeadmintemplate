<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Revision extends Model
{
    protected $guarded = [];

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function store() {
        return $this->belongsTo('App\Store');
    }

    public function revision_products() {
        return $this->hasOne('App\RevisionProducts');
    }
}
