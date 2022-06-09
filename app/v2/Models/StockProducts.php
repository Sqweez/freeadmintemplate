<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\v2\Models\StockProducts
 *
 * @property int $id
 * @property int $product_id
 * @property int $stock_id
 * @method static \Illuminate\Database\Eloquent\Builder|StockProducts newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StockProducts newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StockProducts query()
 * @method static \Illuminate\Database\Eloquent\Builder|StockProducts whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockProducts whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockProducts whereStockId($value)
 * @mixin \Eloquent
 * @property-read mixed $discount
 * @property-read \App\v2\Models\Product $product
 * @property-read \App\v2\Models\Stock $stock
 */
class StockProducts extends Model
{
    protected $guarded = ['id'];
    public $timestamps = false;

    public function product() {
        return $this->belongsTo('App\v2\Models\Product')->withTrashed();
    }

    public function stock() {
        return $this->belongsTo(Stock::class);
    }

    public function getDiscountAttribute() {
        return $this->stock->discount / 100;
    }
}
