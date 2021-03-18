<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\ProductThumb
 *
 * @property int $id
 * @property string $product_image
 * @property int $product_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ProductThumb newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductThumb newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductThumb query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductThumb whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductThumb whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductThumb whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductThumb whereProductImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductThumb whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ProductThumb extends Model
{
    protected $guarded = [];
}
