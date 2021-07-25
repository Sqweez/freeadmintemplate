<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\v2\Models\News
 *
 * @property int $id
 * @property string $title
 * @property string $text
 * @property string $short_text
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\v2\Models\Image[] $news_image
 * @property-read int|null $news_image_count
 * @method static \Illuminate\Database\Eloquent\Builder|News newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|News newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|News query()
 * @method static \Illuminate\Database\Eloquent\Builder|News whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|News whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|News whereShortText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|News whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|News whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|News whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read mixed $products
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\NewsProduct[] $productNews
 * @property-read int|null $product_news_count
 */
class News extends Model
{

    protected $fillable = ['title', 'text', 'short_text'];

    protected $casts = [
        'id' => 'integer'
    ];

    public function news_image() {
        return $this->morphToMany('App\v2\Models\Image', 'imagable', 'imagable');
    }

    public function productNews() {
        return $this->hasMany('App\NewsProduct', 'news_id');
    }

   /* public function products() {
        return $this->hasManyThrough(
            'App\v2\Models\Product',
            'App\NewsProduct',
            'product_id',
            'news_id', 'id', 'id');
    }*/

    public function getProductsAttribute() {
        $product_ids = $this->productNews()->get()->pluck('product_id');
        return Product::find($product_ids);
    }

}
