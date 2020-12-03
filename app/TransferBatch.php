<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransferBatch extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    public function productBatch() {
        return $this->belongsTo('App\ProductBatch', 'batch_id');
    }

    public function product() {
        return $this->belongsTo('App\Product', 'product_id');
    }
}
