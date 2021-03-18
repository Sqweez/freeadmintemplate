<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\ProductImage
 *
 * @property int $id
 * @property string $product_image
 * @property int $product_id
 * @method static \Illuminate\Database\Eloquent\Builder|ProductImage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductImage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductImage query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductImage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductImage whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductImage whereProductImage($value)
 * @mixin \Eloquent
 */
class ProductImage extends Model
{
    protected $guarded = [];

    public $timestamps = false;
}
