<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \App\ManufacturerProducts;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    public function parent()
    {
        return $this->belongsTo('App\Product', 'group_id');
    }

    public function children()
    {
        return $this->hasMany('App\Product', 'group_id');
    }

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

    public function product_images() {
        return $this->hasMany('App\ProductImage', 'product_id');
    }

    public function scopeMain(Builder $query)
    {
        return $query->whereHas('children');
    }

    public function scopeOfCategory($query, $param) {
        if (!count($param)) {
            return $query;
        }
        $ids = CategoryProduct::whereIn('category_id', $param)->pluck('product_id');
        return $query->whereIn('id', $ids);
    }

    public function scopeOfSubcategory($query, $param) {
        if (!count($param)) {
            return $query;
        }
        $ids = SubcategoryProduct::whereIn('subcategory_id', $param)->pluck('product_id');
        return $query->whereIn('id', $ids);
    }

    public function scopeOfBrand($query, $param) {
        if (!count($param)) {
            return $query;
        }
        $ids = ManufacturerProducts::whereIn('manufacturer_id', $param)->pluck('product_id');
        return $query->whereIn('id', $ids);
    }

    public function scopeOfPrice($query, $param) {
        if (!count($param)) {
            return $query;
        }
        return $query->where('product_price', '>=', $param[0])->where('product_price', '<=', $param[1]);
    }
}
