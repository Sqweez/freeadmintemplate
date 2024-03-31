<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\v2\Models\WholesaleOrder
 *
 * @property int $id
 * @property int $wholesale_client_id
 * @property string $phone
 * @property string $email
 * @property string $name
 * @property int $payment_type_id
 * @property int $delivery_type_id
 * @property int|null $delivery_price
 * @property string|null $comment
 * @property int $is_paid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\v2\Models\WholesaleOrderStatusHistory[] $statusHistory
 * @property-read int|null $status_history_count
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleOrder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleOrder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleOrder query()
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleOrder whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleOrder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleOrder whereDeliveryPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleOrder whereDeliveryTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleOrder whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleOrder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleOrder whereIsPaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleOrder whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleOrder wherePaymentTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleOrder wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleOrder whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleOrder whereWholesaleClientId($value)
 * @mixin \Eloquent
 * @property-read \App\v2\Models\WholesaleOrderDeliveryType $deliveryType
 * @property-read \App\v2\Models\WholesaleOrderPaymentType $paymentType
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\v2\Models\WholesaleOrderProduct[] $products
 * @property-read int|null $products_count
 */
class WholesaleOrder extends Model
{
    protected $guarded = ['id'];

    public function statusHistory(): HasMany
    {
        return $this->hasMany(WholesaleOrderStatusHistory::class, 'wholesale_order_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(WholesaleOrderProduct::class);
    }


    public function currentStatus()
    {
        return $this->statusHistory()->latest('changed_at')->first();
    }

    public function paymentType(): BelongsTo
    {
        return $this->belongsTo(WholesaleOrderPaymentType::class, 'payment_type_id');
    }

    public function deliveryType(): BelongsTo
    {
        return $this->belongsTo(WholesaleOrderDeliveryType::class, 'delivery_type_id');
    }
}
