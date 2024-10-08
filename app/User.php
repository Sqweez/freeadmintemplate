<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

/**
 * App\User
 *
 * @property int $id
 * @property int $role_id
 * @property int $store_id
 * @property string|null $token
 * @property string $name
 * @property string $login
 * @property string $password
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\UserRole $role
 * @property-read \App\Store $store
 * @method static \Illuminate\Database\Eloquent\Builder|User login($login)
 * @method static \Illuminate\Database\Eloquent\Builder|User sellers()
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Query\Builder|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User token($token)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|User withTrashed()
 * @method static \Illuminate\Database\Query\Builder|User withoutTrashed()
 * @mixin \Eloquent
 * @property-read mixed $is_super_user
 */
class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;
    protected $guarded = [];

    protected $hidden = [
        'token'
    ];

    const IRON_WEB_STORE = 2;

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::make($password);
    }

    public function store(): BelongsTo {
        return $this->belongsTo(Store::class, 'store_id');
    }


    /**
     * @returns Store[]
     * */
    public function storesInSameCity()
    {
        $cityId = $this->store->city_id;
        return Store::where('city_id', $cityId)->get();
    }

    public function role(): BelongsTo {
        return $this->belongsTo('App\UserRole', 'role_id');
    }

    public function getIsSuperUserAttribute(): bool {
        return in_array($this->role_id, [UserRole::ADMIN_ROLE_ID, UserRole::BOSS_ROLE_ID]);
    }

    public function isFranchise(): bool
    {
        return $this->role_id === __hardcoded(11);
    }

    public function isStoreKeeper(): bool
    {
        return $this->role_id === __hardcoded(7);
    }

    public function isSeller(): bool {
        return $this->role_id === __hardcoded(UserRole::SELLER_ROLE_ID);
    }

    public function scopeLogin($q, $login) {
        $q->where('login', $login);
    }

    public function scopeToken($q, $token) {
        $q->where('token', $token);
    }

    public function scopeSellers($q) {
        return $q->whereIn('role_id', [1, 2, 9]);
    }

    public function hasWorkingAccess(): bool
    {
        return true;
        if (!$this->isSeller()) {
            return true;
        }
        return false;
    }

    public function getShouldOpenShift(): bool
    {
        return false;
        if (!$this->isSeller()) {
            return false;
        }
        return true;
    }
}
