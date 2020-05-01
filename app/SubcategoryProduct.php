<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubcategoryProduct extends Model
{
    protected $table = 'subcategory_product';

    protected $guarded = [];

    public $timestamps = false;

    public function product() {
        return $this->hasOne('App\Product', 'id', 'product_id');
    }

}
