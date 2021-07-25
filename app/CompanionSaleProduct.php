<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\CompanionSaleProduct
 *
 * @property int $id
 * @property int $product_batch_id
 * @property int $product_id
 * @property int $companion_sale_id
 * @property int $purchase_price
 * @property int $product_price
 * @property int $discount
 * @property-read \App\v2\Models\ProductSku $product
 * @property-read \App\CompanionSale $sale
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionSaleProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionSaleProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionSaleProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionSaleProduct whereCompanionSaleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionSaleProduct whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionSaleProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionSaleProduct whereProductBatchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionSaleProduct whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionSaleProduct whereProductPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionSaleProduct wherePurchasePrice($value)
 * @mixin \Eloquent
 */
class CompanionSaleProduct extends Model
{
    protected $guarded = ['id'];
    public $timestamps = false;
    protected $casts = [
        'id' => 'integer',
        'product_id' => 'integer',
        'product_batch_id' => 'integer',
        'companion_sale_id' => 'integer',
        'purchase_price' => 'integer',
        'product_price' => 'integer',
        'discount' => 'integer'
    ];

    public function product() {
        return $this->belongsTo('App\v2\Models\ProductSku');
    }

    public function sale() {
        return $this->belongsTo('App\CompanionSale', 'companion_sale_id');
    }
}
