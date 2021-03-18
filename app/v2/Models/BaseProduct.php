<?php

namespace App\v2\Models;

use App\CategoryProduct;
use App\ManufacturerProducts;
use App\ProductTag;
use App\SubcategoryProduct;
use App\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\v2\Models\BaseProduct
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\v2\Models\AttributeValue[] $attributes
 * @property-read int|null $attributes_count
 * @property-read \App\Category $category
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\v2\Models\Sku[] $children
 * @property-read int|null $children_count
 * @property-read \App\Manufacturer $manufacturer
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Price[] $price
 * @property-read int|null $price_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\v2\Models\Image[] $product_images
 * @property-read int|null $product_images_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\v2\Models\Thumb[] $product_thumbs
 * @property-read int|null $product_thumbs_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ProductBatch[] $quantity
 * @property-read int|null $quantity_count
 * @property-read \App\Subcategory $subcategory
 * @property-read \Illuminate\Database\Eloquent\Collection|Tag[] $tags
 * @property-read int|null $tags_count
 * @method static Builder|BaseProduct inStock($store_id = 1)
 * @method static Builder|BaseProduct isHit($param = 'false')
 * @method static Builder|BaseProduct main()
 * @method static Builder|BaseProduct newModelQuery()
 * @method static Builder|BaseProduct newQuery()
 * @method static Builder|BaseProduct ofBrand($param)
 * @method static Builder|BaseProduct ofCategory($param)
 * @method static Builder|BaseProduct ofPrice($param)
 * @method static Builder|BaseProduct ofSearch($search)
 * @method static Builder|BaseProduct ofSubcategory($param)
 * @method static Builder|BaseProduct ofTag($search)
 * @method static \Illuminate\Database\Query\Builder|BaseProduct onlyTrashed()
 * @method static Builder|BaseProduct query()
 * @method static \Illuminate\Database\Query\Builder|BaseProduct withTrashed()
 * @method static \Illuminate\Database\Query\Builder|BaseProduct withoutTrashed()
 * @mixin \Eloquent
 */
class BaseProduct extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    use SoftDeletes;

    // product fields
    const PRODUCT_NAME = 'product_name';
    const PRODUCT_DESCRIPTION = 'product_description';
    const PRODUCT_PRICE = 'product_price';
    const PRODUCT_BARCODE = 'product_barcode';
    const IS_HIT = 'is_hit';
    const IS_SITE_VISIBLE = 'is_site_visible';

    // relationship fields
    const CATEGORY = 'category';
    const MANUFACTURER = 'manufacturer';
    const ATTRIBUTES = 'attributes';
    const SUBCATEGORY = 'subcategory';
    const ATTRIBUTE_NAMES = 'attributes.attribute_name';
    const TAG = 'tags';
    const PRICE = 'price';
    const PRODUCT_IMAGES = 'product_images';
    const PRODUCT_THUMBS = 'product_thumbs';

    // filters constants
    const FILTER_CATEGORIES = 'category';
    const FILTER_SUBCATEGORIES = 'subcategory';
    const FILTER_BRANDS = 'brands';
    const FILTER_PRICES = 'prices';
    const FILTER_IS_HIT = 'is_hit';
    const FILTER_SEARCH = 'search';

    // cache constants

    const CACHE_PREFIX = 'PRODUCTS_CACHE';


    protected $hidden = ['pivot'];

    protected $casts = [
        'id' => 'integer',
        'product_price' => 'integer',
        'is_hit' => 'boolean',
        'is_site_visible' => 'boolean',
        'group_id' => 'integer'
    ];

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


    public function tags() {
        return $this->morphToMany('App\Tag', 'taggable');
    }

    public function children() {
        return $this->hasMany(Sku::class, 'product_id');
    }

    public function category() {
        return $this->belongsTo('App\Category')
            ->select(['category_name', 'id'])
            ->withDefault([
                'category_name' => 'неизвестно',
                'id' => null
            ]);
    }

    public function subcategory() {
        return $this->belongsTo('App\Subcategory')
            ->select(['subcategory_name', 'id'])
            ->withDefault([
                'subcategory_name' => 'Неизвестно',
                'id' => -1
            ]);
    }

    public function manufacturer() {
        return $this->belongsTo('App\Manufacturer')
            ->select(['manufacturer_name', 'id'])
            ->withDefault([
                'manufacturer_name' => 'Неизвестно',
                'id' => -1
            ]);
    }


    public function attributes() {
        return $this->morphToMany(AttributeValue::class, 'attributable', 'attributable');
    }

    public function quantity() {
        return $this->hasMany('App\ProductBatch', 'product_id');
    }

    public function product_images() {
        return $this->morphToMany('App\v2\Models\Image', 'imagable', 'imagable');
    }

    public function product_thumbs() {
        return $this->morphToMany('App\v2\Models\Thumb', 'thumbable', 'thumbable');
    }

    public function price() {
        return $this->hasMany('App\Price', 'product_id');
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
        return $query->where('product_name', 'like', $search);
    }

    public function scopeOfTag($query, $search) {
        if (!$search) {
            return $query;
        }
        $tags = Tag::where('name', 'like', $search)->pluck('id');
        $ids = ProductTag::whereIn('tag_id', $tags)->pluck('product_id');
        return $query->whereIn('id', $ids);
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
