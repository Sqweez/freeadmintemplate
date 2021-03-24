<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanionSaleProduct extends Model
{
    protected $guarded = ['id'];
    public $timestamps = false;
    protected $casts = [
        'id' => 'integer',
        'product_id' => 'integer',
        'product_batch_id' => 'integer',
        'companion_sale_id' => 'integer',
        'purchase_price' => 'integer',
        'product_price' => 'integer',
        'discount' => 'integer'
    ];

    public function product() {
        return $this->belongsTo('App\v2\Models\ProductSku');
    }

    public function sale() {
        return $this->belongsTo('App\CompanionSale', 'companion_sale_id');
    }
}
