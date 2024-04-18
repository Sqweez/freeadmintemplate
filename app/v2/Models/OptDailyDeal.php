<?php

namespace App\v2\Models;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\v2\Models\OptDailyDeal
 *
 * @property int $id
 * @property string $active_from
 * @property string $active_to
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read bool $is_active
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\v2\Models\OptDailyDealProduct[] $items
 * @property-read int|null $items_count
 * @method static \Illuminate\Database\Eloquent\Builder|OptDailyDeal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OptDailyDeal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OptDailyDeal query()
 * @method static \Illuminate\Database\Eloquent\Builder|OptDailyDeal whereActiveFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OptDailyDeal whereActiveTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OptDailyDeal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OptDailyDeal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OptDailyDeal whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OptDailyDeal extends Model
{
    protected $guarded = [
        'id'
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OptDailyDealProduct::class, 'opt_daily_deal_id');
    }

    public function getIsActiveAttribute(): bool
    {
        $from = Carbon::parse($this->active_from);
        $to = Carbon::parse($this->active_to);
        return now()->greaterThanOrEqualTo($from) && now()->lessThanOrEqualTo($to);
    }

    public function getRemainingTimeAttribute(): string
    {
        $from = Carbon::parse($this->active_from);
        $to = Carbon::parse($this->active_to);
        Carbon::setLocale('ru-RU');
        return $to->diffForHumans(now(), [
            'parts' => 4, // Количество отображаемых частей
            'short' => false, // Не использовать короткие обозначения типа '1м'
            'syntax' => CarbonInterface::DIFF_ABSOLUTE
        ]);
    }
}
