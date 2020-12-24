<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\ProductTag
 *
 * @property int $id
 * @property int $product_id
 * @property int $tag_id
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTag query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTag whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductTag whereTagId($value)
 * @mixin \Eloquent
 * @property-read mixed $taggable_type
 */
class ProductTag extends Model
{
    protected $fillable = ['product_id', 'tag_id'];
    public $timestamps = false;
    protected $appends = ['taggable_type'];

    public function getTaggableTypeAttribute() {
        return 'App\Product';
    }
}
