<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;

class Loyalty extends Model
{
    protected $guarded = [
        'id'
    ];

    protected $casts = [
        'id' => 'integer',
        'discount' => 'integer',
        'cashback' => 'integer'
    ];

/*    public function getDiscountAttribute($value) {
        return ceil($value / 100);
    }

    public function getCashbackAttribute($value) {
        return ceil($value / 100);
    }*/
}
