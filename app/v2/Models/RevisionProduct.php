<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\v2\Models\RevisionProduct
 *
 * @property int $id
 * @property int $revision_id
 * @property int $product_sku_id
 * @property int $product_id
 * @property int|null $count_expected
 * @property int|null $count_actual
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $difference_count
 * @property-read \App\v2\Models\Product $product
 * @property-read \App\v2\Models\Revision $revision
 * @property-read \App\v2\Models\ProductSku $sku
 * @method static \Illuminate\Database\Eloquent\Builder|RevisionProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RevisionProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RevisionProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|RevisionProduct whereCountActual($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RevisionProduct whereCountExpected($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RevisionProduct whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RevisionProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RevisionProduct whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RevisionProduct whereProductSkuId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RevisionProduct whereRevisionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RevisionProduct whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int $revision_file_id
 * @method static \Illuminate\Database\Eloquent\Builder|RevisionProduct whereRevisionFileId($value)
 * @property-read mixed $actual_count_price_total
 * @property-read mixed $expected_count_price_total
 */
class RevisionProduct extends Model
{
    protected $table = 'v2_revision_products';

    protected $guarded = [
        'id'
    ];


    public function revision(): BelongsTo
    {
        return $this->belongsTo(Revision::class, 'revision_id');
    }

    public function sku(): BelongsTo
    {
        return $this->belongsTo(ProductSku::class, 'product_sku_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id')
            ->select(['id', 'product_name', 'category_id', 'manufacturer_id', 'product_price']);
    }

    public function getDifferenceCountAttribute(): ?int
    {
        return $this->count_actual - $this->count_expected;
    }

    public function getExpectedCountPriceTotalAttribute()
    {
        return $this->product->product_price * $this->count_expected;
    }

    public function getActualCountPriceTotalAttribute()
    {
        return $this->product->product_price * $this->count_actual;
    }
}
