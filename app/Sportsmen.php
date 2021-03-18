<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Sportsmen
 *
 * @property int $id
 * @property string $name
 * @property string $instagram
 * @property string|null $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Sportsmen newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Sportsmen newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Sportsmen query()
 * @method static \Illuminate\Database\Eloquent\Builder|Sportsmen whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sportsmen whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sportsmen whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sportsmen whereInstagram($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sportsmen whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sportsmen whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Sportsmen extends Model
{
    protected $guarded = [];
}
