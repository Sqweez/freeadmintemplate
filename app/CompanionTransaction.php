<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanionTransaction extends Model
{
    protected $fillable = [
        'transaction_sum', 'user_id', 'companion_id', 'companion_sale_id'
    ];

    protected $casts = [
        'id' => 'integer',
        'transaction_sum' => 'integer',
        'user_id' => 'integer',
        'companion_id' => 'integer',
        'companion_sale_id' => 'integer',
    ];

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function companion() {
        return $this->belongsTo('App\Store', 'companion_id', 'id');
    }


}
