<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\v2\Models\ProductSaleEarning
 *
 * @property int $id
 * @property int $product_id
 * @property int $percent
 * @property int $store_id
 * @property-read \App\Store $store
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSaleEarning newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSaleEarning newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSaleEarning query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSaleEarning whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSaleEarning wherePercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSaleEarning whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSaleEarning whereStoreId($value)
 * @mixin \Eloquent
 */
class ProductSaleEarning extends Model
{
    public $timestamps = false;
    protected $guarded = [];
    protected $casts = [
        'percent' => 'integer',
        'product_id' => 'integer',
        'store_id' => 'integer',
    ];

    public function product() {
        return $this->belongsTo('App\v2\Product');
    }

    public function store() {
        return $this->belongsTo('App\Store');
    }
}
