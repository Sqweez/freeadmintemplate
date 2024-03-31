<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\v2\Models\UserCartItem
 *
 * @property int $id
 * @property int $product_id
 * @property int $cart_id
 * @property int $count
 * @property int $discount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\v2\Models\UserCart $cart
 * @property-read \App\v2\Models\ProductSku $product
 * @method static \Illuminate\Database\Eloquent\Builder|UserCartItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserCartItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserCartItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserCartItem whereCartId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCartItem whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCartItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCartItem whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCartItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCartItem whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCartItem whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class UserCartItem extends Model
{
    protected $guarded = ['id'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(ProductSku::class, 'product_id');
    }

    public function cart()
    {
        return $this->belongsTo(UserCart::class, 'cart_id');
    }
}
