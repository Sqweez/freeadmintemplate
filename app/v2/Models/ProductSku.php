<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

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
 * @property-read mixed $is_kaspi_visible
 * @property-read mixed $kaspi_product_price
 * @property-read mixed $product_name_web
 * @property int $margin_type_id
 * @property-read mixed $additional_subcategories
 * @property-read \App\MarginType $margin_type
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSku whereMarginTypeId($value)
 */
class ProductSku extends Model
{

    use SoftDeletes;

    protected $guarded = ['id'];
    protected $table = 'product_sku';

    const PRODUCT_SKU_WITH_ADMIN_LIST =  [
        'product:id,product_name,product_price,category_id,subcategory_id,manufacturer_id,grouping_attribute_id,product_name_web,is_kaspi_visible,is_iherb',
        'product.category', 'product.manufacturer', 'product.attributes', 'product.price',
        'product.attributes.attribute_name', 'attributes', 'attributes.attribute_name', 'margin_type',
    ];

    const PRODUCT_SKU_MODERATOR_LIST = [
        'product:id,product_name,product_price,category_id,subcategory_id,manufacturer_id,grouping_attribute_id,product_description,product_name_web',
        'product.category', 'product.manufacturer', 'product.attributes', 'product.tags',
        'product.attributes.attribute_name', 'attributes', 'attributes.attribute_name', 'product.product_images', 'margin_type'
    ];

    const PRODUCT_SKU_WITH_CART_LIST = [
        'product:id,product_name,product_price,manufacturer_id,grouping_attribute_id',
        'product.manufacturer', 'product.attributes',
        'product.attributes.attribute_name', 'attributes', 'attributes.attribute_name', 'margin_type'
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
        $batches = $this->hasMany('App\ProductBatch', 'product_id');
        if (is_null($batches)) {
            return [];
        }
        return $batches;
    }

    public function relativeSku() {
        return $this->hasMany('App\v2\Models\ProductSku', 'product_id', 'product_id');
    }

    public function margin_type(): BelongsTo {
        return $this->belongsTo('App\MarginType');
    }


    public function getProductPriceAttribute() {
        return $this->self_price == 0 ? $this->product->product_price : $this->self_price;
    }

    public function getProductNameAttribute() {
        return $this->product->product_name ?? 'Неизвестно';
    }

    public function getProductNameWebAttribute() {
        return $this->product->product_name_web;
    }

    public function getProductDescriptionAttribute() {
        return $this->product->product_description;
    }

    public function getIsIherbAttribute() {
        return $this->product->is_iherb;
    }

    public function getCategoryAttribute() {
        return $this->product->category;
    }

    public function getSubcategoryAttribute() {
        return $this->product->subcategory;
    }

    public function getAdditionalSubcategoriesAttribute() {
        return $this->product->additionalSubcategories;
    }

    public function getManufacturerAttribute() {
        return $this->product->manufacturer ?? 'Неизвестно';
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

    public function getKaspiProductPriceAttribute() {
        return $this->product->kaspi_product_price;
    }

    public function getIsKaspiVisibleAttribute() {
        return $this->product->is_kaspi_visible;
    }

    public function mergeAttributes($attributes, $productAttributes): Collection {
        return collect($attributes)->map(function ($attribute) {
            return [
                'attribute_value' => $attribute['attribute_value'],
                'attribute_name' => $attribute['attribute_name']['attribute_name'],
            ];
        })->merge(collect($productAttributes)->map(function ($attribute) {
            return [
                'attribute_value' => $attribute['attribute_value'],
                'attribute_name' => $attribute['attribute_name']['attribute_name'],
            ];
        }));
    }

    public function getExcelNameAttribute() {
        $attributes = $this
            ->mergeAttributes($this['attributes'], $this->product['attributes'])
            ->pluck('attribute_value')
            ->join(' | ');
        return sprintf(
            '%s %s %s',
            $this['manufacturer']['manufacturer_name'],
            $this['product_name'],
            $attributes
        );
    }
}
