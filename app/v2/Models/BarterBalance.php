<?php

namespace App\v2\Models;

use App\Client;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\v2\Models\BarterBalance
 *
 * @property int $id
 * @property int $client_id
 * @property int $user_id
 * @property int $amount
 * @property int $is_active
 * @property string|null $active_until
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Client $client
 * @property-read mixed $is_usable
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder|BarterBalance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BarterBalance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BarterBalance query()
 * @method static \Illuminate\Database\Eloquent\Builder|BarterBalance whereActiveUntil($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BarterBalance whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BarterBalance whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BarterBalance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BarterBalance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BarterBalance whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BarterBalance whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BarterBalance whereUserId($value)
 * @mixin \Eloquent
 */
class BarterBalance extends Model
{
    protected $guarded = ['id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function getIsUsableAttribute()
    {
        return $this->is_active && (is_null($this->active_until) || Carbon::parse($this->active_until)->greaterThan(
                    today()
                ));
    }
}
