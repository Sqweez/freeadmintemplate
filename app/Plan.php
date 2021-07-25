<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Plan
 *
 * @property int $id
 * @property int $store_id
 * @property int $month_plan
 * @property int $week_plan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Store $store
 * @method static \Illuminate\Database\Eloquent\Builder|Plan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Plan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Plan query()
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereMonthPlan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereWeekPlan($value)
 * @mixin \Eloquent
 * @property int $prize
 * @method static \Illuminate\Database\Eloquent\Builder|Plan wherePrize($value)
 */
class Plan extends Model
{
    protected $guarded = [];

    public function store() {
        return $this->belongsTo('App\Store', 'store_id');
    }
}
