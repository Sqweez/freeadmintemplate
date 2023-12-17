<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

/**
 * App\Models\FitUser
 *
 * @property int $id
 * @property int $gym_id
 * @property string $name
 * @property string $login
 * @property string $fit_role_id
 * @property string $password
 * @property string|null $token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\FitGym $gym
 * @property-read \App\Models\FitRoles $role
 * @method static \Illuminate\Database\Eloquent\Builder|FitUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FitUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FitUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|FitUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitUser whereFitRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitUser whereGymId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitUser whereLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitUser whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitUser wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitUser whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitUser whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FitUser extends Authenticatable
{
    protected $guarded = [
        'id'
    ];

    protected $hidden = [
        'token'
    ];

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::make($password);
    }

    public function role(): BelongsTo {
        return $this->belongsTo(FitRoles::class, 'fit_role_id');
    }

    public function gym(): BelongsTo
    {
        return $this->belongsTo(FitGym::class, 'gym_id');
    }
}
