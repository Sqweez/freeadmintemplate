<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;

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
 */
class City extends Model
{
    protected $fillable = ['name', 'region_id', 'delivery_cost'];
    public $timestamps = false;
    protected $casts = [
        'delivery_cost' => 'integer',
        'delivery_threshold' => 'integer'
    ];
}
