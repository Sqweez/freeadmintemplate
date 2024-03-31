<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\v2\Models\WholesaleOrderProduct
 *
 * @property int $id
 * @property int $wholesale_order_id
 * @property int $product_id
 * @property int $price
 * @property int $currency_id
 * @property int $purchase_price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleOrderProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleOrderProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleOrderProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleOrderProduct whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleOrderProduct whereCurrencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleOrderProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleOrderProduct wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleOrderProduct whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleOrderProduct wherePurchasePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleOrderProduct whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleOrderProduct whereWholesaleOrderId($value)
 * @mixin \Eloquent
 */
class WholesaleOrderProduct extends Model
{
    protected $guarded = ['id'];
}
