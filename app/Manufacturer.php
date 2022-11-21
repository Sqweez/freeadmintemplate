<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * App\Manufacturer
 *
 * @property int $id
 * @property string $manufacturer_name
 * @property string $manufacturer_img
 * @property string|null $manufacturer_description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Manufacturer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Manufacturer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Manufacturer query()
 * @method static \Illuminate\Database\Eloquent\Builder|Manufacturer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manufacturer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manufacturer whereManufacturerDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manufacturer whereManufacturerImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manufacturer whereManufacturerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manufacturer whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Product[] $products
 * @property-read int|null $products_count
 * @property bool $show_on_main
 * @method static \Illuminate\Database\Eloquent\Builder|Manufacturer whereShowOnMain($value)
 */
class Manufacturer extends Model
{
    protected $guarded = [
        'id'
    ];

    protected $casts = [
        'id' => 'integer',
        'show_on_main' => 'boolean'
    ];

    public function products(): HasMany {
        return $this->hasMany(\App\v2\Models\Product::class);
    }
}
