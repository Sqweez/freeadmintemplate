<?php

namespace App\v2\Models;

use App\Store;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\v2\Models\BestBefore
 *
 * @property int $id
 * @property int $product_sku_id
 * @property int $quantity
 * @property string $best_before
 * @property int $store_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read int $days_to_expire
 * @property-read bool $is_expired
 * @property-read \App\v2\Models\ProductSku $sku
 * @property-read Store $store
 * @method static \Illuminate\Database\Eloquent\Builder|BestBefore newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BestBefore newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BestBefore query()
 * @method static \Illuminate\Database\Eloquent\Builder|BestBefore whereBestBefore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BestBefore whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BestBefore whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BestBefore whereProductSkuId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BestBefore whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BestBefore whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BestBefore whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BestBefore extends Model
{
    protected $guarded = ['id'];

    public function sku(): BelongsTo {
        return $this->belongsTo(ProductSku::class, 'product_sku_id');
    }

    public function store(): BelongsTo {
        return $this->belongsTo(Store::class);
    }

    public function getIsExpiredAttribute(): bool {
        return now()->diffInDays(Carbon::parse($this->best_before)) <= 0;
    }

    public function getDaysToExpireAttribute(): int {
        return now()->diffInDays(Carbon::parse($this->best_before));
    }
}
