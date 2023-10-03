<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\ClientTransaction
 *
 * @property int $id
 * @property int $amount
 * @property int $user_id
 * @property int $client_id
 * @property int $sale_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ClientTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientTransaction whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientTransaction whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientTransaction whereSaleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientTransaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientTransaction whereUserId($value)
 * @mixin \Eloquent
 */
class ClientTransaction extends Model
{
    protected $guarded = [];

    const TYPE_CASHBACK = 1;
    const TYPE_PARTNER_ROYALTY = 2;

    public function setAmountAttribute($value) {
        $this->attributes['amount'] = ceil($value);
    }

    public function client(): BelongsTo {
        return $this->belongsTo(Client::class, 'client_id');
    }
}
