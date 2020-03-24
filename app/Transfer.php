<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    protected $guarded = [];


    public function parent_store() {
        return $this->belongsTo('App\Store', 'parent_store_id');
    }

    public function child_store() {
        return $this->belongsTo('App\Store', 'child_store_id');
    }

    public function batches() {
        return $this->hasMany('App\TransferBatch', 'transfer_id');
    }
}
