<?php

namespace App\v2\Models;

use App\Store;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\v2\Models\City
 *
 * @property int $id
 * @property string $name
 * @property int $region_id
 * @method static \Illuminate\Database\Eloquent\Builder|City newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|City newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|City query()
 * @method static \Illuminate\Database\Eloquent\Builder|City whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereRegionId($value)
 * @mixin \Eloquent
 * @property int $delivery_cost
 * @property int $delivery_threshold
 * @method static \Illuminate\Database\Eloquent\Builder|City whereDeliveryCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereDeliveryThreshold($value)
 * @property string $kaspi_city_id
 * @method static \Illuminate\Database\Eloquent\Builder|City whereKaspiCityId($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|Store[] $stores
 * @property-read int|null $stores_count
 * @property int $country_id
 * @method static \Illuminate\Database\Eloquent\Builder|City whereCountryId($value)
 */
class City extends Model
{
    protected $fillable = ['name', 'region_id', 'delivery_cost', 'country_id'];
    public $timestamps = false;
    protected $casts = [
        'delivery_cost' => 'integer',
        'delivery_threshold' => 'integer'
    ];


    public function stores(): HasMany
    {
        return $this->hasMany(Store::class, 'city_id');
    }
}
