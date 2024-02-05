<?php

namespace App\v2\Models;

use App\Sale;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\v2\Models\Certificate
 *
 * @property int $id
 * @property string|null $barcode
 * @property int $user_id
 * @property int $amount
 * @property string $expired_at
 * @property bool $active
 * @property int $used_sale_id
 * @property int $sale_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $final_amount
 * @property-read mixed $used
 * @property-read Sale $sale
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate query()
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereBarcode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereExpiredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereSaleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereUsedSaleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Certificate whereUserId($value)
 * @mixin \Eloquent
 */
class Certificate extends Model
{

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'amount' => 'integer',
        'sale_id' => 'integer',
        'used_sale_id' => 'integer',
        'active' => 'boolean',
    ];

    protected $fillable = [
        'barcode', 'user_id', 'amount', 'expired_at', 'active', 'used_sale_id', 'sale_id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo('App\User');
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo('App\Sale');
    }

    public function getUsedAttribute(): bool
    {
        return $this->used_sale_id !== 0;
    }


    public function getFinalAmountAttribute(): int
    {
        return ($this->used_sale_id === 0) ? $this->amount : 0;
    }

    public function getCertificateName(): string
    {
        return trim(
            sprintf("Сертификат на сумму %s тенге %s",
                $this->amount,
                $this->used ? '(использован)' : ''
            )
        );
    }
}
