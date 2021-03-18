<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\StoreType
 *
 * @property int $id
 * @property string $type
 * @method static \Illuminate\Database\Eloquent\Builder|StoreType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StoreType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StoreType query()
 * @method static \Illuminate\Database\Eloquent\Builder|StoreType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StoreType whereType($value)
 * @mixin \Eloquent
 */
class StoreType extends Model
{
    protected $guarded = [];
    public $timestamps = false;
}
