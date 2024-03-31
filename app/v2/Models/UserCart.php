<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * App\v2\Models\UserCart
 *
 * @property int $id
 * @property int $user_id
 * @property string $userable_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\v2\Models\UserCartItem[] $items
 * @property-read int|null $items_count
 * @property-read Model|\Eloquent $user
 * @method static \Illuminate\Database\Eloquent\Builder|UserCart newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserCart newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserCart query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserCart whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCart whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCart whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCart whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCart whereUserableType($value)
 * @mixin \Eloquent
 */
class UserCart extends Model
{
    protected $guarded = [
        'id'
    ];

    public function user(): MorphTo
    {
        return $this->morphTo(WholesaleClient::class, 'userable_type', 'user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(UserCartItem::class, 'cart_id');
    }
}
