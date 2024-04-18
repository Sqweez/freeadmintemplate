<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\v2\Models\OptDailyDealProduct
 *
 * @property int $id
 * @property int $opt_daily_deal_id
 * @property int $product_id
 * @property int $discount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\v2\Models\Product $products
 * @method static \Illuminate\Database\Eloquent\Builder|OptDailyDealProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OptDailyDealProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OptDailyDealProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|OptDailyDealProduct whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OptDailyDealProduct whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OptDailyDealProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OptDailyDealProduct whereOptDailyDealId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OptDailyDealProduct whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OptDailyDealProduct whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OptDailyDealProduct extends Model
{
    protected $guarded = [
        'id'
    ];

    public function products(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function dailyDeal(): BelongsTo
    {
        return $this->belongsTo(OptDailyDeal::class, 'opt_daily_deal_id');
    }
}
