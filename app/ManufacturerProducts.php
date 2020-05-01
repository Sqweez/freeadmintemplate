<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ManufacturerProducts extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    public function manufacturer() {
        return $this->hasOne('App\Manufacturer', 'id', 'manufacturer_id');
    }
}
