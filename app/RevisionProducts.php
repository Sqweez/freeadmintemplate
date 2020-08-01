<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RevisionProducts extends Model
{
    protected $guarded = [];
    public $timestamps = false;

    public function product() {
        return $this->belongsTo('App\Product', 'product_id');
    }
}
