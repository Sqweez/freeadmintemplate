<?php

namespace App;

use App\ManufacturerProducts;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Request;

class Product extends Model
{

    use SoftDeletes;

    protected $guarded = [];

    public $timestamps = false;

    protected static function boot() {
        parent::boot();

        static::creating(function ($query) {
            $query->product_barcode = $query->product_barcode ?? "";
        });

        static::updating(function ($query) {
            $query->product_barcode = $query->product_barcode ?? "";
        });
    }

    public function tag() {
        return $this->belongsToMany(Tag::class, 'product_tags');
    }

    public function parent()
    {
        return $this->belongsTo('App\Product', 'group_id');
    }

    public function children()
    {
        return $this->hasMany('App\Product', 'group_id', 'group_id');
    }

    public function categories() {
        return $this->belongsToMany('App\Category', 'category_product');
    }

    public function subcategories() {
        return $this->belongsToMany('App\Subcategory', 'subcategory_product');
    }

    public function attributes() {
        return $this->hasMany('App\AttributeProduct', 'product_id');
    }

    public function manufacturer() {
        return $this->belongsToMany('App\Manufacturer', 'manufacturer_products');
    }

    public function quantity() {
        return $this->hasMany('App\ProductBatch', 'product_id');
    }

    public function product_quantity() {
        return $this->hasMany(ProductQuantity::class, 'product_id');
    }

    public function product_images() {
        return $this->hasMany('App\ProductImage', 'product_id');
    }

    public function product_thumbs() {
        return $this->hasMany('App\ProductThumb', 'product_id');
    }

    public function price() {
        return $this->hasMany('App\Price', 'product_id');
    }

    public function getCurrentPriceAttribute() {
        return 1000;
    }

    public function scopeMain(Builder $query)
    {
        return $query->whereHas('children');
    }

    public function scopeInStock($query, $store_id = 1) {
        $query->whereHas('quantity', function ($query) use ($store_id) {
            $query->where('store_id', $store_id)->where('quantity', '>', 0);
        });
    }

    public function scopeOfSearch($query, $search) {
        if (!$search) {
            return $query;
        }
        $query->where('product_name', 'like', $search);
    }

    public function scopeOfTag($query, $search) {
        if (!$search) {
            return $query;
        }
        $tags = Tag::where('name', 'like', $search)->pluck('id');
        $ids = ProductTag::whereIn('tag_id', $tags)->pluck('product_id');
        $query->whereIn('id', $ids);
    }

    public function scopeOfCategory($query, $param) {
        if (count($param) === 0) {
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

    public function scopeIsHit($query, $param = 'false') {
        if ($param === 'false') {
            return $query;
        }
        return $query->where('is_hit', true);
    }
}
