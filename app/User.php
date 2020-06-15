<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;
    protected $guarded = [];

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::make($password);
    }

    public function store() {
        return $this->belongsTo('App\Store', 'store_id');
    }

    public function role() {
        return $this->belongsTo('App\UserRole', 'role_id');
    }

    public function scopeLogin($q, $login) {
        $q->where('login', $login);
    }

    public function scopeToken($q, $token) {
        $q->where('token', $token);
    }

}
