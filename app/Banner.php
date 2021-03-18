<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Banner
 *
 * @property int $id
 * @property string $image
 * @property int $is_active
 * @property string|null $description
 * @property int $order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Banner newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Banner newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Banner query()
 * @method static \Illuminate\Database\Eloquent\Builder|Banner whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Banner whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Banner whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Banner whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Banner whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Banner whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Banner whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $mobile_image
 * @method static \Illuminate\Database\Eloquent\Builder|Banner whereMobileImage($value)
 */
class Banner extends Model
{
    protected $guarded = [
        'is_active' => 'boolean',
        'order' => 'integer'
    ];

    protected $fillable = ['image', 'is_active', 'order', 'description', 'mobile_image'];
}
