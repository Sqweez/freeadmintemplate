<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\ProductBatch
 *
 * @property int $id
 * @property int $product_id
 * @property int $quantity
 * @property int $store_id
 * @property int $purchase_price
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $arrival_id
 * @property Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBatch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBatch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBatch ofProduct($product)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBatch ofStore($store)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBatch Positive()
 * @method static \Illuminate\Database\Query\Builder|ProductBatch onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBatch query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBatch whereArrivalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBatch whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBatch whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBatch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBatch whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBatch wherePurchasePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBatch whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBatch whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBatch whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ProductBatch withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ProductBatch withoutTrashed()
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBatch quantitiesOfStore($store_id)
 * @property-read \App\Store $store
 * @property-read \App\v2\Models\ProductSku $product
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBatch positive()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBatch quantities()
 */
class ProductBatch extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'quantity' => 'integer',
        'product_id' => 'integer',
        'id' => 'integer'
    ];

    public function product() {
        return $this->belongsTo('App\v2\Models\ProductSku', 'product_id');
    }

    public function store() {
        return $this->belongsTo('App\Store', 'store_id');
    }

    public function scopeOfStore($query, $store) {
        $query->where('store_id', $store);
    }

    public function scopeOfProduct($query, $product) {
        $query->where('product_id', $product);
    }

    public function scopePositive($query) {
        $query->where('quantity', '>', 0);
    }

    public function scopeQuantitiesOfStore($query, $store_id) {
        return $query->where('quantity', '>', 0)
            ->whereStoreId($store_id)
            ->groupBy('product_id')
            ->select('product_id')
            ->selectRaw('sum(quantity) as quantity');
    }

    public function scopeQuantities($query) {
        return $query
            ->where('quantity', '>', 0)
            ->with('store:id,name')
            ->select(['id', 'store_id', 'quantity', 'product_id']);
    }

}
