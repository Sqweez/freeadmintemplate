<?php

namespace App\v2\Models;

use Illuminate\Database\Eloquent\Model;

/* @deprecated  */
/**
 * App\v2\Models\ClientCard
 *
 * @property int $id
 * @property string $code
 * @property int $loyalty_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ClientCard newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientCard newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientCard query()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientCard whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientCard whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientCard whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientCard whereLoyaltyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientCard whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ClientCard extends Model
{
    protected $guarded = ['id'];
}
