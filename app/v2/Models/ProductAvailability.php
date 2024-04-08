<?php

namespace App\v2\Models;

use App\Store;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\v2\Models\ProductAvailability
 *
 * @property int $id
 * @property int $product_sku_id
 * @property int $product_id
 * @property int $quantity
 * @property int $store_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\v2\Models\Product $product
 * @property-read \App\v2\Models\ProductSku $productSku
 * @property-read Store $store
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAvailability newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAvailability newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAvailability query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAvailability whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAvailability whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAvailability whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAvailability whereProductSkuId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAvailability whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAvailability whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductAvailability whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ProductAvailability extends Model
{
    protected $guarded = [
        'id'
    ];

    public function productSku(): BelongsTo
    {
        return $this->belongsTo(ProductSku::class, 'product_sku_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id');
    }
}
