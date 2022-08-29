<?php

namespace App\v2\Models;

use App\Store;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\v2\Models\WithDrawal
 *
 * @property int $id
 * @property int $amount
 * @property int $store_id
 * @property int $user_id
 * @property string|null $description
 * @property string|null $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read bool $can_delete
 * @property-read Store $store
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder|WithDrawal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WithDrawal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WithDrawal query()
 * @method static \Illuminate\Database\Eloquent\Builder|WithDrawal whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WithDrawal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WithDrawal whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WithDrawal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WithDrawal whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WithDrawal whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WithDrawal whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WithDrawal whereUserId($value)
 * @mixin \Eloquent
 */
class WithDrawal extends Model
{
    protected $table = 'withdrawals';

    protected $guarded = [
        'id'
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function store(): BelongsTo {
        return $this->belongsTo(Store::class);
    }

    public function getCanDeleteAttribute(): bool {
        return auth()->user()->is_super_user;
    }
}
