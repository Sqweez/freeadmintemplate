<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\ArrivalProducts
 *
 * @property int $id
 * @property int $product_id
 * @property int $arrival_id
 * @property int $count
 * @property int $purchase_price
 * @property-read \App\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder|ArrivalProducts newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ArrivalProducts newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ArrivalProducts query()
 * @method static \Illuminate\Database\Eloquent\Builder|ArrivalProducts whereArrivalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArrivalProducts whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArrivalProducts whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArrivalProducts whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArrivalProducts wherePurchasePrice($value)
 * @mixin \Eloquent
 */
class ArrivalProducts extends Model
{
    protected $guarded = [];
    public $timestamps = false;

    public function product() {
        return $this->belongsTo('App\Product')->withTrashed();
    }
}
