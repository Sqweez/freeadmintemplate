<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;

class BrandMotivation extends Model
{
    public $timestamps = false;

    protected $guarded = ['id'];

    protected $casts = [
        'id' => 'integer',
        'amount' => 'array',
        'brands' => 'array'
    ];

}
