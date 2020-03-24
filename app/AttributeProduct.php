<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AttributeProduct extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    public function attribute_name() {
        return $this->belongsTo('App\Attribute', 'attribute_id');
    }
}
