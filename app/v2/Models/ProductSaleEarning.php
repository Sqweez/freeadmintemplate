<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSaleEarning extends Model
{
    public $timestamps = false;
    protected $guarded = [];
    protected $casts = [
        'percent' => 'integer',
        'product_id' => 'integer',
        'store_id' => 'integer',
    ];

    public function product() {
        return $this->belongsTo('App\v2\Product');
    }

    public function store() {
        return $this->belongsTo('App\Store');
    }
}
