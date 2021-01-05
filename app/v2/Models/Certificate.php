<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'amount' => 'integer',
        'sale_id' => 'integer',
        'used_sale_id' => 'integer',
        'active' => 'boolean',
    ];

    protected $fillable = [
        'barcode', 'user_id', 'amount', 'expired_at', 'active', 'used_sale_id', 'sale_id'
    ];

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function sale() {
        return $this->belongsTo('App\Sale');
    }

    public function getUsedAttribute() {
        return $this->used_sale_id !== 0;
    }


    public function getFinalAmountAttribute() {
        return ($this->used_sale_id === 0) ? $this->amount : 0;
    }
}
