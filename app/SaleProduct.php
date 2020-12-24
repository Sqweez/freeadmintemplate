<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Model;

/**
 * App\SaleProduct
 *
 * @property int $id
 * @property int $product_batch_id
 * @property int $product_id
 * @property int $sale_id
 * @property int $purchase_price
 * @property int $product_price
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
 */
class SaleProduct extends Model
{
    protected $guarded = [];
    public $timestamps = false;

    protected $casts = [
        'purchase_price' => 'integer',
        'product_price' => 'integer'
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

    public function brothers() {
        return $this->hasMany('App\SaleProduct', 'id', 'id');
    }

    /*public function getCountAttribute() {
        return static::query()->where('sale_id', $this->sale_id)->where('product_id', $this->product_id)->count();
    }*/
}
