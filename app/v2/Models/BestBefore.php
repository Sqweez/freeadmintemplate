<?php

namespace App\v2\Models;

use App\Store;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BestBefore extends Model
{
    protected $guarded = ['id'];

    public function sku(): BelongsTo {
        return $this->belongsTo(ProductSku::class, 'product_sku_id');
    }

    public function store(): BelongsTo {
        return $this->belongsTo(Store::class);
    }

    public function getIsExpiredAttribute(): bool {
        return now()->diffInDays(Carbon::parse($this->best_before)) <= 0;
    }

    public function getDaysToExpireAttribute(): int {
        return now()->diffInDays(Carbon::parse($this->best_before));
    }
}
