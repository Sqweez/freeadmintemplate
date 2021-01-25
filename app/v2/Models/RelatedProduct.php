<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;

class RelatedProduct extends Model
{
    public $timestamps = false;

    protected $fillable = ['category_id', 'product_id'];

    public function product() {
        return $this->belongsTo('App\v2\Models\Product', 'product_id');
    }
}
