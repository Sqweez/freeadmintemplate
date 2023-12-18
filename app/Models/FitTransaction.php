<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\FitTransaction
 *
 * @property int $id
 * @property int $client_id
 * @property int $amount
 * @property int $user_id
 * @property int $gym_id
 * @property string|null $description
 * @property int $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\FitClient $client
 * @property-read \App\Models\FitGym $gym
 * @property-read \App\Models\FitUser $user
 * @method static \Illuminate\Database\Eloquent\Builder|FitTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FitTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FitTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|FitTransaction whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitTransaction whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitTransaction whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitTransaction whereGymId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitTransaction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitTransaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitTransaction whereUserId($value)
 * @mixin \Eloquent
 */
class FitTransaction extends Model
{
    protected $guarded = [
        'id'
    ];

    const TYPE_CASH = 1;
    const TYPE_CASHLESS = 2;

    public function client(): BelongsTo
    {
        return $this->belongsTo(FitClient::class, 'client_id');
    }

    public function gym(): BelongsTo
    {
        return $this->belongsTo(FitGym::class, 'gym_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(FitUser::class, 'user_id');
    }
}
