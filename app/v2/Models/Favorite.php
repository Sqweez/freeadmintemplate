<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\v2\Models\Favorite
 *
 * @property int $id
 * @property int $product_id
 * @property string $user_token
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite query()
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite whereUserToken($value)
 * @mixin \Eloquent
 */
class Favorite extends Model
{

    protected $table = 'favorites';
    public $timestamps = false;

    protected $fillable = [
        'user_token',
        'product_id'
    ];
}
