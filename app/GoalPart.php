<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoalPart extends Model
{
    protected $guarded = [];
    public $timestamps = false;

    public function products() {
        return $this->hasMany('App\GoalPartProducts', 'goal_part_id');
    }

    public function category() {
        return $this->belongsTo('App\Category')->withDefault([
            'category_name' => ''
        ]);
    }

    public function subcategory() {
        return $this->belongsTo('App\Subcategory')->withDefault([
            'subcategory_name' => ''
        ]);
    }

}
