<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\SaleProduct
 *
 * @property int $id
 * @property int $product_batch_id
 * @property int $product_id
 * @property int $sale_id
 * @property int $purchase_price
 * @property int $product_price
 * @property int $discount
 * @property-read Product $product
 * @property-read Sale $sale
 * @method static \Illuminate\Database\Eloquent\Builder|SaleProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SaleProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SaleProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|SaleProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleProduct whereProductBatchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleProduct whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleProduct whereProductPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleProduct wherePurchasePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SaleProduct whereSaleId($value)
 * @mixin Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|SaleProduct[] $brothers
 * @property-read int|null $brothers_count
 * @property-read mixed $final_price
 * @property-read mixed $margin
 * @method static \Illuminate\Database\Eloquent\Builder|SaleProduct whereDiscount($value)
 * @property-read \App\ProductBatch $batch
 * @property-read mixed $balance
 * @property-read mixed $booking
 * @property-read mixed $certificate
 * @property-read mixed $final_sale_price
 * @property-read mixed $kaspi_red
 */
class SaleProduct extends Model
{
    protected $guarded = [];
    public $timestamps = false;

    protected $casts = [
        'purchase_price' => 'integer',
        'product_price' => 'integer',
        'discount' => 'integer'
    ];

    public function product() {
        return $this->belongsTo('App\v2\Models\ProductSku', 'product_id')->withDefault([
            'product_name' => 'Неизвестно',
            'attributes' => [],
            'manufacturer' => collect([])
        ])->withTrashed();
    }

    public function sale() {
        return $this->belongsTo('App\Sale', 'sale_id');
    }

    public function brothers(): \Illuminate\Database\Eloquent\Relations\HasMany {
        return $this->hasMany('App\SaleProduct', 'id', 'id');
    }

    public function getFinalPriceAttribute() {
        return $this->product_price - ($this->discount /  100) * $this->product_price;
    }

    public function getKaspiRedAttribute(): bool {
        return !!$this->sale->kaspi_red;
    }

    public function getCertificateAttribute(): v2\Models\Certificate {
        return $this->sale->certificate;
    }

    public function getBookingAttribute(): ?v2\Models\Booking {
        return $this->sale->booking;
    }

    public function getBalanceAttribute(): int {
        return $this->sale->balance;
    }

    public function getFinalSalePriceAttribute() {
        return $this->getFinalPriceAttribute();
    }

    public function getMarginAttribute() {
        return ceil($this->discount === 100 ? 0 : $this->final_price - $this->purchase_price);
    }

    public function batch(): BelongsTo {
        return $this->belongsTo('App\ProductBatch', 'product_batch_id');
    }
}
