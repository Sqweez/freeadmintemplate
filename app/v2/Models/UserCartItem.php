<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserCartItem extends Model
{
    protected $guarded = ['id'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(ProductSku::class, 'product_id');
    }

    public function cart()
    {
        return $this->belongsTo(UserCart::class, 'cart_id');
    }
}
