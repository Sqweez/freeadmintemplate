<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\v2\Models\Thumb
 *
 * @property int $id
 * @property string|null $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Thumb newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Thumb newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Thumb query()
 * @method static \Illuminate\Database\Eloquent\Builder|Thumb whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Thumb whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Thumb whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Thumb whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int|null $image_id
 * @method static \Illuminate\Database\Eloquent\Builder|Thumb whereImageId($value)
 */
class Thumb extends Model
{
    protected $hidden = ['pivot'];

    protected $fillable = ['image'];
}
