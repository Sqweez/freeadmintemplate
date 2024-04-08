<?php

namespace App;

use App\v2\Models\ProductSku;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\CartProduct
 *
 * @property int $id
 * @property int $cart_id
 * @property int $product_id
 * @property int $count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder|CartProduct cart($cart_id)
 * @method static \Illuminate\Database\Eloquent\Builder|CartProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CartProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CartProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|CartProduct store($store_id)
 * @method static \Illuminate\Database\Eloquent\Builder|CartProduct whereCartId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartProduct whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartProduct whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartProduct whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartProduct whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|CartProduct ofProduct($product_id)
 * @method static \Illuminate\Database\Eloquent\Builder|CartProduct product($product_id)
 * @property int $discount
 * @method static \Illuminate\Database\Eloquent\Builder|CartProduct whereDiscount($value)
 * @property int|null $kit_id
 * @property string|null $kit_slug
 * @property int $has_other_discounts
 * @method static \Illuminate\Database\Eloquent\Builder|CartProduct whereHasOtherDiscounts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartProduct whereKitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartProduct whereKitSlug($value)
 */
class CartProduct extends Model
{
    protected $guarded = [];

    protected $casts = [
        'count' => 'integer'
    ];

    public function product(): BelongsTo {
        return $this->belongsTo(ProductSku::class, 'product_id');
    }

    public function scopeCart($query, $cart_id) {
        $query->where('cart_id', $cart_id);
    }

    public function scopeProduct($query, $product_id) {
        $query->where('product_id', $product_id);
    }

    public function scopeStore($query, $store_id) {
        //
    }

}
