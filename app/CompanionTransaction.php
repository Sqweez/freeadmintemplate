<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\CompanionTransaction
 *
 * @property int $id
 * @property int $transaction_sum
 * @property int $companion_id
 * @property int $user_id
 * @property int $companion_sale_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $type
 * @property-read \App\Store $companion
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionTransaction whereCompanionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionTransaction whereCompanionSaleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionTransaction whereTransactionSum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionTransaction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionTransaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompanionTransaction whereUserId($value)
 * @mixin \Eloquent
 */
class CompanionTransaction extends Model
{
    protected $fillable = [
        'transaction_sum', 'user_id', 'companion_id', 'companion_sale_id', 'type'
    ];


    const COMPANION_OWN_BALANCE_TYPE = 1;
    const COMPANION_IRON_BALANCE_TYPE = 2;

    protected $casts = [
        'id' => 'integer',
        'transaction_sum' => 'integer',
        'user_id' => 'integer',
        'companion_id' => 'integer',
        'companion_sale_id' => 'integer',
        'type' => 'integer'
    ];

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function companion() {
        return $this->belongsTo('App\Store', 'companion_id', 'id');
    }


}
