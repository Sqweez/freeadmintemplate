<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{

    protected $table = 'favorites';
    public $timestamps = false;

    protected $fillable = [
        'user_token',
        'product_id'
    ];
}
