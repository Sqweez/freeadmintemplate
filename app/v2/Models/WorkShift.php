<?php

namespace App\v2\Models;

use App\Store;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\v2\Models\WorkShift
 *
 * @property int $id
 * @property int $user_id
 * @property int $store_id
 * @property string $shift_date
 * @property string|null $closed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Store $store
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder|WorkShift newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkShift newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkShift query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkShift whereClosedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkShift whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkShift whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkShift whereShiftDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkShift whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkShift whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkShift whereUserId($value)
 * @mixin \Eloquent
 */
class WorkShift extends Model
{
    protected $guarded = [
        'id'
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id')->select(['id', 'name']);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->select(['id', 'name']);
    }

    public function open($userId, $storeId)
    {
        return $this->create([
            'user_id' => $userId,
            'store_id' => $storeId,
        ]);
    }

    public function close()
    {
        $this->closed_at = now();
        $this->save();
    }
}
