<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\v2\Models\WholesaleFavorite
 *
 * @property int $id
 * @property int $wholesale_client_id
 * @property int $product_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleFavorite newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleFavorite newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleFavorite query()
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleFavorite whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleFavorite whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleFavorite whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleFavorite whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WholesaleFavorite whereWholesaleClientId($value)
 * @mixin \Eloquent
 */
class WholesaleFavorite extends Model
{
    protected $guarded = [
        'id'
    ];
}
