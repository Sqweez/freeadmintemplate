<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\v2\Models\Loyalty
 *
 * @property int $id
 * @property string $name
 * @property int $discount
 * @property int $cashback
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Loyalty newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Loyalty newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Loyalty query()
 * @method static \Illuminate\Database\Eloquent\Builder|Loyalty whereCashback($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Loyalty whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Loyalty whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Loyalty whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Loyalty whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Loyalty whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Loyalty extends Model
{
    protected $guarded = [
        'id'
    ];

    protected $casts = [
        'id' => 'integer',
        'discount' => 'integer',
        'cashback' => 'integer'
    ];

/*    public function getDiscountAttribute($value) {
        return ceil($value / 100);
    }

    public function getCashbackAttribute($value) {
        return ceil($value / 100);
    }*/
}
