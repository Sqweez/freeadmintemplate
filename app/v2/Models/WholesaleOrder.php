<?php

namespace App\v2\Models;

use Carbon\Carbon;
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
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleOrder byClient(int $clientId)
 * @property-read string|null $formatted_expected_arrival_date
 * @property-read mixed $position_count
 * @property-read mixed $total_price
 * @property string|null $expected_arrival_date
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleOrder whereExpectedArrivalDate($value)
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


    public function currentStatus(): HasMany
    {
        return $this->statusHistory()->latest('changed_at')->limit(1);
    }

    public function paymentType(): BelongsTo
    {
        return $this->belongsTo(WholesaleOrderPaymentType::class, 'payment_type_id');
    }

    public function deliveryType(): BelongsTo
    {
        return $this->belongsTo(WholesaleOrderDeliveryType::class, 'delivery_type_id');
    }

    /**
     * @throws \Exception
     */
    public function changeStatus($statusId): WholesaleOrder
    {
        // Проверьте, существует ли данный статус
        $status = WholesaleOrderStatus::find($statusId);
        if (!$status) {
            throw new \Exception('Неверный статус');
        }

        WholesaleOrderStatusHistory::create([
            'wholesale_order_id' => $this->id,
            'wholesale_status_id' => $statusId,
            'changed_at' => now(),
        ]);

        return $this;
    }

    /**
     * @throws \Exception
     */
    public function setStatusCreate(): WholesaleOrder
    {
        return $this->changeStatus(__hardcoded(1));
    }

    public function scopeByClient($query, int $clientId)
    {
        return $query->where('wholesale_client_id', $clientId);
    }

    public function getTotalPriceAttribute()
    {
        return $this->products->sum('price');
    }

    public function getPositionCountAttribute()
    {
        return $this->products->pluck('product_id')->unique()->count();
    }

    public function getFormattedExpectedArrivalDateAttribute(): ?string
    {
        if ($this->expected_arrival_date) {
            return Carbon::parse($this->expected_arrival_date)->format('d.m.Y');
        }
        return  null;
    }

}
