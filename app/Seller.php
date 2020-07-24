<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    protected $guarded = [];

    public function rating() {
        return $this->hasMany('App\SellerRating');
    }

    public function avg_rating() {
        $this->rating()->average('rating');
    }
}
