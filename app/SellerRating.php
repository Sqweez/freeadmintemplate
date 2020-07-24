<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SellerRating extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    public function criteria() {
        return $this->belongsTo('App\RatingCriteria');
    }

    public function criteria_name() {
        $this->criteria()->criteria;
    }
}
