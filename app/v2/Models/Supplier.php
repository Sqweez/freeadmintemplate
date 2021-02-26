<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = ['supplier_name', 'user_id'];

    protected $casts = [
        'user_id' => 'integer',
        'id' => 'integer'
    ];

    public $timestamps = false;

    public function products() {
        return $this->hasMany('App\v2\Models\Product', 'supplier_id');
    }

    public function user() {
        return $this->belongsTo('App\User');
    }
}
