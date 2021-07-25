<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\NewsProduct
 *
 * @property int $id
 * @property int $news_id
 * @property int $product_id
 * @property-read \App\v2\Models\News $news
 * @property-read \App\v2\Models\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder|NewsProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewsProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewsProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|NewsProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewsProduct whereNewsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewsProduct whereProductId($value)
 * @mixin \Eloquent
 */
class NewsProduct extends Model
{
    protected $table = 'news_products';
    public $timestamps = false;
    protected $guarded = ['id'];

    protected $casts = [
        'id' => 'integer',
        'product_id' => 'integer',
        'news_id' => 'integer'
    ];

    public function news() {
        return $this->belongsTo('App\v2\Models\News', 'news_id');
    }

    public function product() {
        return $this->belongsTo('App\v2\Models\Product', 'product_id');
    }
}
