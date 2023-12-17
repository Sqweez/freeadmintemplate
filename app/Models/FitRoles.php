<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FitRoles
 *
 * @property int $id
 * @property string $name
 * @method static \Illuminate\Database\Eloquent\Builder|FitRoles newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FitRoles newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FitRoles query()
 * @method static \Illuminate\Database\Eloquent\Builder|FitRoles whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FitRoles whereName($value)
 * @mixin \Eloquent
 */
class FitRoles extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];
}
