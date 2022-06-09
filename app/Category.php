<?php

namespace App;

use App\v2\Models\SeoText;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Cache;
use function foo\func;

/**
 * App\Category
 *
 * @property int $id
 * @property string $category_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $category_img
 * @property string $category_slug
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Subcategory[] $subcategories
 * @property-read int|null $subcategories_count
 * @method static \Illuminate\Database\Eloquent\Builder|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category ofSlug($slug)
 * @method static \Illuminate\Database\Eloquent\Builder|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereCategoryImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereCategoryName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereCategorySlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\CategoryProduct[] $category_product
 * @property-read int|null $category_product_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\v2\Models\RelatedProduct[] $relatedProducts
 * @property-read int|null $related_products_count
 * @property int $is_site_visible
 * @method static \Illuminate\Database\Eloquent\Builder|Category site()
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereIsSiteVisible($value)
 * @property-read SeoText|null $seoText
 */
class Category extends Model
{
    protected $guarded = [];

    protected $hidden = ['pivot'];


    public function subcategories() {
        return $this->hasMany('App\Subcategory', 'category_id');
    }

    public static function boot() {
        parent::boot();
        static::deleting(function($category) {
            $category->subcategories()->delete();
            Cache::forget('categories');
            Cache::forget('shop-categories');
        });

        static::creating(function() {
            Cache::forget('categories');
            Cache::forget('shop-categories');
        });

        static::updating(function() {
            Cache::forget('categories');
            Cache::forget('shop-categories');
        });
    }

    public function relatedProducts() {
        return $this->hasMany('App\v2\Models\RelatedProduct', 'category_id');
    }

    public function seoText(): MorphOne {
        return $this->morphOne(SeoText::class, 'entity');
    }

    private function clearCache() {
        Cache::forget('categories');
        Cache::forget('shop-categories');
    }

    public function scopeOfSlug($query, $slug) {
        return $query->where('category_slug', $slug);
    }

    public function category_product() {
        return $this->hasMany('App\CategoryProduct', 'category_id');
    }

    public function scopeSite($q) {
        return $q->where('is_site_visible', true);
    }

}
