<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\ProductQuantity
 *
 * @property int $id
 * @property int $product_id
 * @property int $store_id
 * @property int $quantity
 * @method static \Illuminate\Database\Eloquent\Builder|ProductQuantity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductQuantity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductQuantity query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductQuantity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductQuantity whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductQuantity whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductQuantity whereStoreId($value)
 * @mixin \Eloquent
 */
class ProductQuantity extends Model
{
    public $timestamps = false;
    protected $guarded = [];
}
