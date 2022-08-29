<?php

namespace App;

use App\v2\Models\SeoText;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Carbon;

/**
 * App\Subcategory
 *
 * @property int $id
 * @property string $subcategory_name
 * @property int $category_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $subcategory_slug
 * @method static \Illuminate\Database\Eloquent\Builder|Subcategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subcategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subcategory ofSlug($slug)
 * @method static \Illuminate\Database\Eloquent\Builder|Subcategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|Subcategory whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subcategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subcategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subcategory whereSubcategoryName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subcategory whereSubcategorySlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subcategory whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int $is_site_visible
 * @method static \Illuminate\Database\Eloquent\Builder|Subcategory site()
 * @method static \Illuminate\Database\Eloquent\Builder|Subcategory whereIsSiteVisible($value)
 * @property-read SeoText|null $seoText
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_h1
 * @method static \Illuminate\Database\Eloquent\Builder|Subcategory whereMetaDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subcategory whereMetaH1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subcategory whereMetaTitle($value)
 */
class Subcategory extends Model
{
    protected $guarded = [];

    public function scopeOfSlug($query, $slug) {
        return $query->where('subcategory_slug', $slug);
    }

    public function scopeSite($q) {
        return $q->where('is_site_visible', true);
    }

    public function seoText(): MorphOne {
        return $this->morphOne(SeoText::class, 'entity');
    }
}
