<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\v2\Models\RelatedProduct
 *
 * @property int $id
 * @property int $category_id
 * @property int $product_id
 * @property-read \App\v2\Models\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder|RelatedProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RelatedProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RelatedProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|RelatedProduct whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RelatedProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RelatedProduct whereProductId($value)
 * @mixin \Eloquent
 */
class RelatedProduct extends Model
{
    public $timestamps = false;

    protected $fillable = ['category_id', 'product_id'];

    public function product() {
        return $this->belongsTo('App\v2\Models\Product', 'product_id');
    }
}
