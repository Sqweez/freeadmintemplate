<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

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
