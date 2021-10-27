<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\v2\Models\Stock
 *
 * @property int $id
 * @property string $title
 * @property int $discount
 * @property string $started_at
 * @property string $finished_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\v2\Models\StockProducts[] $products
 * @property-read int|null $products_count
 * @method static \Illuminate\Database\Eloquent\Builder|Stock active()
 * @method static \Illuminate\Database\Eloquent\Builder|Stock newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Stock newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Stock query()
 * @method static \Illuminate\Database\Eloquent\Builder|Stock whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stock whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stock whereFinishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stock whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stock whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stock whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stock whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Stock extends Model
{
    protected $guarded = [
        'id'
    ];

    public function products() {
        return $this->hasMany(StockProducts::class);
    }

    public function scopeActive($q) {
        return $q->where('started_at', '<=', now())
            ->where('finished_at', '>=', now());
    }
}
