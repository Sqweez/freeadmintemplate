<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\v2\Models\ProductSku
 *
 * @property int $id
 * @property int $product_id
 * @property int $self_price
 * @property string $product_barcode
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\v2\Models\AttributeValue[] $attributes
 * @property-read int|null $attributes_count
 * @property-read mixed $category
 * @property-read mixed $discount_price
 * @property-read mixed $grouping_attribute_id
 * @property-read mixed $is_hit
 * @property-read mixed $is_site_visible
 * @property-read mixed $manufacturer
 * @property-read mixed $product_description
 * @property-read mixed $product_name
 * @property-read mixed $product_price
 * @property-read mixed $subcategory
 * @property-read \App\v2\Models\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSku newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSku newQuery()
 * @method static \Illuminate\Database\Query\Builder|ProductSku onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSku query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSku whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSku whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSku whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSku whereProductBarcode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSku whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSku whereSelfPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSku whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ProductSku withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ProductSku withoutTrashed()
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ProductBatch[] $batches
 * @property-read int|null $batches_count
 * @property-read mixed $general_images
 * @property-read mixed $general_thumbs
 * @property-read mixed $prices
 * @property-read mixed $sku_count
 * @property-read mixed $tags
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\v2\Models\Image[] $product_images
 * @property-read int|null $product_images_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\v2\Models\Thumb[] $product_thumbs
 * @property-read int|null $product_thumbs_count
 * @property-read \Illuminate\Database\Eloquent\Collection|ProductSku[] $relativeSku
 * @property-read int|null $relative_sku_count
 * @property-read mixed $all_attributes
 */
class ProductSku extends Model
{

    use SoftDeletes;

    protected $guarded = ['id'];
    protected $table = 'product_sku';

    const PRODUCT_SKU_WITH_ADMIN_LIST =  [
        'product:id,product_name,product_price,category_id,manufacturer_id,grouping_attribute_id',
        'product.category', 'product.manufacturer', 'product.attributes',
        'product.attributes.attribute_name', 'attributes', 'attributes.attribute_name'
    ];

    const PRODUCT_SKU_WITH_CART_LIST = [
        'product:id,product_name,product_price,manufacturer_id,grouping_attribute_id',
        'product.manufacturer', 'product.attributes',
        'product.attributes.attribute_name', 'attributes', 'attributes.attribute_name'
    ];

    const PRODUCT_SKU_IMAGES = 'product_sku_images';
    const PRODUCT_SKU_THUMBS = 'product_sku_images';

    protected $casts = [
        'id' => 'integer',
        'self_price' => 'integer',
    ];


    const WITH_PRODUCT = 'product:id,product_name,product_price,category_id,manufacturer_id';

    public function attributes() {
        return $this->morphToMany(AttributeValue::class, 'attributable', 'attributable');
    }

    public function product_images() {
        return $this->morphToMany('App\v2\Models\Image', 'imagable', 'imagable');
    }

    public function product_thumbs() {
        return $this->morphToMany('App\v2\Models\Thumb', 'thumbable', 'thumbable');
    }

    public function product() {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function batches() {
        return $this->hasMany('App\ProductBatch', 'product_id');
    }

    public function relativeSku() {
        return $this->hasMany('App\v2\Models\ProductSku', 'product_id', 'product_id');
    }


    public function getProductPriceAttribute() {
        return $this->self_price == 0 ? $this->product->product_price : $this->self_price;
    }

    public function getProductNameAttribute() {
        return $this->product->product_name;
    }

    public function getProductDescriptionAttribute() {
        return $this->product->product_description;
    }

    public function getCategoryAttribute() {
        return $this->product->category;
    }

    public function getSubcategoryAttribute() {
        return $this->product->subcategory;
    }

    public function getManufacturerAttribute() {
        return $this->product->manufacturer;
    }

    public function getIsHitAttribute() {
        return $this->product->is_hit;
    }

    public function getIsSiteVisibleAttribute() {
        return $this->product->is_site_visible;
    }

    public function getDiscountPriceAttribute() {
        return $this->product->discount_price;
    }

    public function getGroupingAttributeIdAttribute() {
        return $this->product->grouping_attribute_id;
    }

    public function getTagsAttribute() {
        return $this->product->tags;
    }

    public function getPricesAttribute() {
        return $this->product->price;
    }

    public function getGeneralImagesAttribute() {
        return $this->product->product_images;
    }

    public function getGeneralThumbsAttribute() {
        return $this->product->product_thumbs;
    }

    public function getSkuCountAttribute() {
        return $this->product->sku->count();
    }

    public function getQuantity($store_id) {
        return $this->batches()->whereStoreId($store_id)->sum('quantity');
    }

    public function getAllAttributesAttribute() {
        return collect($this->attributes)->merge(collect($this->product->attributes));
    }
}