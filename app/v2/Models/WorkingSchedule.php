<?php

namespace App\v2\Models;

use App\Store;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\v2\Models\WorkingSchedule
 *
 * @property int $id
 * @property string $date
 * @property int $store_id
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Store $store
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingSchedule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingSchedule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingSchedule query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingSchedule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingSchedule whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingSchedule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingSchedule whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingSchedule whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingSchedule whereUserId($value)
 * @mixin \Eloquent
 */
class WorkingSchedule extends Model
{
    protected $guarded = ['id'];


    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
