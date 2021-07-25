<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\TransferBatch
 *
 * @property int $id
 * @property int $transfer_id
 * @property int $batch_id
 * @property int $product_id
 * @property int $is_transferred
 * @property-read \App\Product $product
 * @property-read \App\ProductBatch $productBatch
 * @method static \Illuminate\Database\Eloquent\Builder|TransferBatch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TransferBatch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TransferBatch query()
 * @method static \Illuminate\Database\Eloquent\Builder|TransferBatch whereBatchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransferBatch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransferBatch whereIsTransferred($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransferBatch whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransferBatch whereTransferId($value)
 * @mixin \Eloquent
 * @property int $discount
 * @method static \Illuminate\Database\Eloquent\Builder|TransferBatch whereDiscount($value)
 */
class TransferBatch extends Model
{
    protected $guarded = [
        'id'
    ];

    protected $casts = [
        'discount' => 'integer',
    ];

    public $timestamps = false;

    public function productBatch() {
        return $this->belongsTo('App\ProductBatch', 'batch_id')->withTrashed();
    }

    public function product() {
        return $this->belongsTo('App\v2\Models\ProductSku', 'product_id')->withTrashed();
    }
}
