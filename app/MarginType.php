<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MarginType extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'salary_rules' => 'array',
        'partner_cashback_rules' => 'array'
    ];
}
