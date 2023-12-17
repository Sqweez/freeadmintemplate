<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\FitGym
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\FitUser[] $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|FitGym newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FitGym newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FitGym query()
 * @method static \Illuminate\Database\Eloquent\Builder|FitGym whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitGym whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitGym whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitGym whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FitGym extends Model
{
    protected $guarded = ['id'];

    public function users(): HasMany
    {
        return $this->hasMany(FitUser::class, 'gym_id');
    }
}
