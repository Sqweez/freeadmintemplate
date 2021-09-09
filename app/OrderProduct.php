<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\OrderProduct
 *
 * @property int $id
 * @property int $product_batch_id
 * @property int $product_id
 * @property int $order_id
 * @property int $purchase_price
 * @property int $product_price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereProductBatchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereProductPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct wherePurchasePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \App\ProductBatch $batch
 */
class OrderProduct extends Model
{
    protected $guarded = [];

    public function product() {
        return $this->belongsTo('App\v2\Models\ProductSku', 'product_id')->withTrashed();
    }

    public function batch() {
        return $this->belongsTo('App\ProductBatch', 'product_batch_id');
    }
}
