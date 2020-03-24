<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    public function categories() {
        return $this->belongsToMany('App\Category', 'category_product');
    }

    public function subcategories() {
        return $this->belongsToMany('App\Subcategory', 'subcategory_product');
    }

    public function attributes() {
        return $this->hasMany('App\AttributeProduct', 'product_id');
        //return $this->belongsToMany('App\Attribute', 'attribute_products', 'product_id', 'attribute_id');
    }

    public function manufacturer() {
        return $this->belongsToMany('App\Manufacturer', 'manufacturer_products');
    }

    public function quantity() {
        return $this->hasMany('App\ProductBatch', 'product_id');
    }
}
