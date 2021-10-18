<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;

class Footer extends Model
{
    protected $table = 'footer';
    public $timestamps = false;
    protected $guarded = [
        'id'
    ];

    protected $casts = [
        'addresses' => 'array',
        'contacts' => 'array'
    ];
}
