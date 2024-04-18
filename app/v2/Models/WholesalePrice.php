<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\v2\Models\WholesalePrice
 *
 * @property int $id
 * @property int $price
 * @property int $currency_id
 * @property int $product_id
 * @property-read \App\v2\Models\Currency $currency
 * @method static \Illuminate\Database\Eloquent\Builder|WholesalePrice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WholesalePrice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WholesalePrice query()
 * @method static \Illuminate\Database\Eloquent\Builder|WholesalePrice whereCurrencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WholesalePrice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WholesalePrice wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WholesalePrice whereProductId($value)
 * @mixin \Eloquent
 * @property-read string $formatted_price
 */
class WholesalePrice extends Model
{
    protected $guarded = [
        'id'
    ];

    public $timestamps = false;

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function getFormattedPriceAttribute(): string
    {
        return price_format($this->price, $this->currency->unicode_symbol, 2);
    }
}
