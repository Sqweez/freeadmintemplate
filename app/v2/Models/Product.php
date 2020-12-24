<?php

namespace App\v2\Models;

use App\AttributeProduct;
use App\CategoryProduct;
use App\ManufacturerProducts;
use App\Price;
use App\ProductBatch;
use App\ProductImage;
use App\ProductQuantity;
use App\ProductTag;
use App\ProductThumb;
use App\SubcategoryProduct;
use App\Tag;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;


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
 * @property Carbon|null $deleted_at
 * @property-read Collection|AttributeProduct[] $attributes
 * @property-read int|null $attributes_count
 * @property-read Collection|CategoryProduct $category
 * @property-read int|null $categories_count
 * @property-read Collection|Product[] $children
 * @property-read int|null $children_count
 * @property-read mixed $current_price
 * @property-read Collection|ManufacturerProducts $manufacturer
 * @property-read int|null $manufacturer_count
 * @property-read Product|null $parent
 * @property-read Collection|Price[] $price
 * @property-read int|null $price_count
 * @property-read Collection|ProductImage[] $product_images
 * @property-read int|null $product_images_count
 * @property-read Collection|ProductQuantity[] $product_quantity
 * @property-read int|null $product_quantity_count
 * @property-read Collection|ProductThumb[] $product_thumbs
 * @property-read int|null $product_thumbs_count
 * @property-read Collection|ProductBatch[] $quantity
 * @property-read int|null $quantity_count
 * @property-read Collection|SubcategoryProduct $subcategory
 * @property-read int|null $subcategories_count
 * @property-read Collection|Tag[] $tags
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
 * @method static Builder|Product with($relations)()
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
 * @property-read int|null $tags_count
 * @property int|null $category_id
 * @property int|null $manufacturer_id
 * @method static Builder|Product whereCategoryId($value)
 * @method static Builder|Product whereManufacturerId($value)
 * @property int $product_discount_price
 * @property int $grouping_attribute_id
 * @property int $subcategory_id
 * @property-read Collection|\App\v2\Models\ProductSku[] $sku
 * @property-read int|null $sku_count
 * @method static Builder|Product whereGroupingAttributeId($value)
 * @method static Builder|Product whereProductDiscountPrice($value)
 * @method static Builder|Product whereSubcategoryId($value)
 */
class Product extends Model
{

    use SoftDeletes;

    // product fields
    const PRODUCT_NAME = 'product_name';
    const PRODUCT_DESCRIPTION = 'product_description';
    const PRODUCT_PRICE = 'product_price';
    const PRODUCT_BARCODE = 'product_barcode';
    const IS_HIT = 'is_hit';
    const IS_SITE_VISIBLE = 'is_site_visible';

    // relationship fields
    const SKU = 'sku';
    const SKU_ATTRIBUTES = 'sku.attributes';
    const CATEGORY = 'category';
    const CATEGORY_ID = 'category_id';
    const MANUFACTURER = 'manufacturer';
    const MANUFACTURER_ID = 'manufacturer_id';
    const SUBCATEGORY = 'subcategory';
    const SUBCATEGORY_ID = 'subcategory_id';
    const ATTRIBUTES = 'attributes';
    const ATTRIBUTE_NAMES = 'attributes.attribute_name';
    const GROUPING_ATTRIBUTE_ID = 'grouping_attribute_id';
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

    protected $guarded = ['id'];

    protected $hidden = ['pivot'];

    protected $casts = [
        'id' => 'integer',
        'product_price' => 'integer',
        'is_hit' => 'boolean',
        'is_site_visible' => 'boolean',
        'group_id' => 'integer'
    ];

    public $timestamps = true;


    public function sku()
    {
        return $this->hasMany(ProductSku::class);
    }

    public function attributes() {
        return $this->morphToMany(AttributeValue::class, 'attributable', 'attributable');
    }

    public function tags() {
        return $this->morphToMany('App\Tag', 'taggable');
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

    public function price() {
        return $this->hasMany('App\Price', 'product_id');
    }


    /*public function manufacturer() {
        return $this->hasOne('App\ManufacturerProducts', 'product_id')
            ->withDefault([
                'manufacturer_id' => -1
            ]);
    }*/

    /* public function manufacturer() {
         return $this->belongsTo('App\Manufacturer', 'manufacturer_id')->select(['manufacturer_name', 'id'])->withDefault(['manufacturer_name' => 'неизвестно', 'id' => null]);;
     }*/


    public function quantity() {
        return $this->hasMany('App\ProductBatch', 'product_id');
    }

    public function product_images() {
        return $this->morphToMany('App\v2\Models\Image', 'imagable', 'imagable');
    }

    public function product_thumbs() {
        return $this->morphToMany('App\v2\Models\Thumb', 'thumbable', 'thumbable');
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
