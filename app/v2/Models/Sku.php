<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\v2\Models\Sku
 *
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
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Price[] $price
 * @property-read int|null $price_count
 * @property-read \App\v2\Models\BaseProduct $product
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\v2\Models\Image[] $product_images
 * @property-read int|null $product_images_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\v2\Models\Thumb[] $product_thumbs
 * @property-read int|null $product_thumbs_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ProductBatch[] $quantity
 * @property-read int|null $quantity_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Tag[] $tags
 * @property-read int|null $tags_count
 * @method static \Illuminate\Database\Eloquent\Builder|Sku newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Sku newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Sku query()
 * @mixin \Eloquent
 */
class Sku extends Model
{
    protected $guarded = ['id'];

    protected $hidden = ['self_price'];

    protected $appends = ['product_name'];

    protected $casts = [
        'id' => 'integer',
        'product_price' => 'integer',
        'is_hit' => 'boolean',
        'is_site_visible' => 'boolean',
        'discount_price' => 'integer'
    ];

    public function product() {
        return $this->belongsTo(BaseProduct::class, 'product_id', 'id');
    }

    public function attributes() {
        return $this->morphToMany(AttributeValue::class, 'attributable', 'attributable');
    }

  /*  public function attribute() {
        return $this->attributes()->where('attribute_id', $this->grouping_attribute_id);
    }*/


    public function quantity() {
        return $this->hasMany('App\ProductBatch', 'product_id');
    }

    public function tags() {
        return $this->morphToMany('App\Tag', 'taggable', 'taggables');
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

    public function getProductPriceAttribute() {
        return $this->self_price === 0 ? $this->product->product_price : $this->self_price;
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
}
