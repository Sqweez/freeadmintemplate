<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\FitClient
 *
 * @property int $id
 * @property string $name
 * @property string $phone
 * @property string $birth_date
 * @property string|null $card
 * @property int $gym_id
 * @property int $fit_user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\FitGym $gym
 * @property-read \App\Models\FitUser $user
 * @method static \Illuminate\Database\Eloquent\Builder|FitClient newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FitClient newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FitClient query()
 * @method static \Illuminate\Database\Eloquent\Builder|FitClient whereBirthDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitClient whereCard($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitClient whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitClient whereFitUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitClient whereGymId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitClient whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitClient whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitClient wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitClient whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string|null $pass
 * @method static \Illuminate\Database\Eloquent\Builder|FitClient wherePass($value)
 */
class FitClient extends Model
{
    protected $guarded = ['id'];

    public function gym(): BelongsTo
    {
        return $this->belongsTo(FitGym::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(FitUser::class, 'fit_user_id');
    }
}
