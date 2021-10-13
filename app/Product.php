<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Product
 *
 * @property int $id
 * @property string $product_name
 * @property string|null $product_description
 * @property int $product_price
 * @property string $product_barcode
 * @property int|null $group_id
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property int $is_hit
 * @property int $is_site_visible
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AttributeProduct[] $attributes
 * @property-read int|null $attributes_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Category[] $categories
 * @property-read int|null $categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Product[] $children
 * @property-read int|null $children_count
 * @property-read mixed $current_price
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Manufacturer[] $manufacturer
 * @property-read int|null $manufacturer_count
 * @property-read Product|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Price[] $price
 * @property-read int|null $price_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ProductImage[] $product_images
 * @property-read int|null $product_images_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ProductQuantity[] $product_quantity
 * @property-read int|null $product_quantity_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ProductThumb[] $product_thumbs
 * @property-read int|null $product_thumbs_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ProductBatch[] $quantity
 * @property-read int|null $quantity_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Subcategory[] $subcategories
 * @property-read int|null $subcategories_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Tag[] $tag
 * @property-read int|null $tag_count
 * @method static Builder|Product inStock($store_id = 1)
 * @method static Builder|Product isHit($param = 'false')
 * @method static Builder|Product main()
 * @method static Builder|Product newModelQuery()
 * @method static Builder|Product newQuery()
 * @method static Builder|Product ofBrand($param)
 * @method static Builder|Product ofCategory($param)
 * @method static Builder|Product ofPrice($param)
 * @method static Builder|Product ofSearch($search)
 * @method static Builder|Product ofSubcategory($param)
 * @method static Builder|Product ofTag($search)
 * @method static \Illuminate\Database\Query\Builder|Product onlyTrashed()
 * @method static Builder|Product query()
 * @method static Builder|Product whereCreatedAt($value)
 * @method static Builder|Product whereDeletedAt($value)
 * @method static Builder|Product whereGroupId($value)
 * @method static Builder|Product whereId($value)
 * @method static Builder|Product whereIsHit($value)
 * @method static Builder|Product whereIsSiteVisible($value)
 * @method static Builder|Product whereProductBarcode($value)
 * @method static Builder|Product whereProductDescription($value)
 * @method static Builder|Product whereProductName($value)
 * @method static Builder|Product whereProductPrice($value)
 * @method static Builder|Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Product withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Product withoutTrashed()
 * @mixin \Eloquent
 * @property int|null $category_id
 * @property int|null $manufacturer_id
 * @method static Builder|Product whereCategoryId($value)
 * @method static Builder|Product whereManufacturerId($value)
 * @property int $product_discount_price
 * @property int $grouping_attribute_id
 * @property int $subcategory_id
 * @method static Builder|Product whereGroupingAttributeId($value)
 * @method static Builder|Product whereProductDiscountPrice($value)
 * @method static Builder|Product whereSubcategoryId($value)
 * @property int $is_kaspi_visible
 * @property int $kaspi_product_price
 * @method static Builder|Product whereIsKaspiVisible($value)
 * @method static Builder|Product whereKaspiProductPrice($value)
 * @property int|null $supplier_id
 * @property string $meta_title
 * @property string|null $meta_description
 * @method static Builder|Product whereMetaDescription($value)
 * @method static Builder|Product whereMetaTitle($value)
 * @method static Builder|Product whereSupplierId($value)
 * @property string $product_name_web
 * @method static Builder|Product whereProductNameWeb($value)
 */
class Product extends Model {

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

    public function parent() {
        return $this->belongsTo('App\Product', 'group_id');
    }

    public function children() {
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

    public function scopeMain(Builder $query) {
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
