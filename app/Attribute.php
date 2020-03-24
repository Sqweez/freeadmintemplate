<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $guarded = [];

    public function value() {
        return $this->hasOne('App\AttributeProduct', 'attribute_id');
    }

    public $timestamps = false;
}
