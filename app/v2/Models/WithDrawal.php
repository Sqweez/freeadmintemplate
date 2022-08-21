<?php

namespace App\v2\Models;

use App\Store;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
